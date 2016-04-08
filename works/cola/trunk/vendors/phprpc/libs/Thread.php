<?php
/* SVN FILE:: $Id: Thread.php 4360 2010-11-01 07:37:24Z sparkwang $ */
/**
 * php thread(process) class
 *
 * @package			phprpc
 * @subpackage		server
 * @copyright 		Copyright (c) 2010 tencent
 * @author			sparkwang
 * @version			$LastChangedRevision: 4360 $ 
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$LastChangedDate: 2010-11-01 15:37:24 +0800 (一, 2010-11-01) $
 */

/**
 * Php thread(process), using libevent and socketpair
 *
 * @package phprpc
 * @subpackage libs
 * @author sparkwang
 */
class Thread {
	
	protected $handle_ = null;
	protected $eventbase_ = null;
	protected $socketEvent_ = null;
	protected $pid_ = -1;
	protected $child_ = false;
	protected $buf_ = '';
	protected $callbacktimeout_ = 750;
	protected $callbackflag_ = false;
	protected $callbackobj_;
	protected $callbackargs_;

	public $context = array();	
	public $callback = false;
	public $proxy = null;
	
	/**
	 * Set the parent socket event base
	 *
	 * @param resource $base event base
	 * @return void
	 * @author sparkwang
	 */
	function setEventBase($base) {
		$this->eventbase_ = $base;
	}
	
	/**
	 * Get the parent socket event base
	 *
	 * @return resource
	 * @author sparkwang
	 */
	function getEventBase() {
		return $this->eventbase_;
	}
	
	/**
	 * Get thread pid
	 *
	 * @return void
	 * @author sparkwang
	 */
	function getPid() {
		return $this->pid_;
	}
	
	/**
	 * Set thread async process call back
	 *
	 * @param mixed $callback Call back function
	 * @param mixed $args Arguments pass to the call back function
	 * @return void
	 * @author sparkwang
	 */
	function setCallBack($callback, $args = null) {
		$this->callback = true;
		$this->callbackobj_ = $callback;
		$this->callbackargs_= $args;
	}
	
	/**
	 * Indicate if the thread is busy now
	 *
	 * @return void
	 * @author sparkwang
	 */
	function isBusy() {
		return $this->callback && $this->callbackflag_;
	}
	
	/**
	 * Call method in/out thread(child or parent)
	 *
	 * @param string $method Method name
	 * @param array $args Method arguments
	 * @param array $context Context array
	 * @param boolean $callback Indicate if this call will be call back
	 * @return void
	 * @author sparkwang
	 */
	function rpc($method, $args = array(), $context = array(), $callback = false) {
		if ($this->callback) {
			$this->waitForCallback_();
		}
		$this->callbackflag_ = true;
		$call = array($method, $args, $context, $callback);
		$call = serialize($call);
		$this->transport_->	writeAll($call);
	}
	
	/**
	 * On thread peer call
	 *
	 * @return void
	 * @author sparkwang
	 */
	function onCall() {
		$this->callbackflag_ = false;
		if ($data = $this->transport_->readAll()) {
			$call = unserialize($data);
			list($method, $args, $context, $callback) = $call;
			try {
				$this->context = $context;
				$ret = call_user_func_array(array($this, $method), $args);
				if ($callback) {
					$this->rpc('onCallBack', array($ret));
				}
			} catch (Exception $e){
				$this->rpc('onError', array($e));
			}
		}
	}
	
	/**
	 * On call back
	 *
	 * @param mixed $args Arguments pass to the call back function. See setCallBack
	 * @return void
	 * @author sparkwang
	 */
	function onCallBack($args) {
		if ($this->callback && $this->callbackobj_) {
			call_user_func_array($this->callbackobj_, array($args, $this->callbackargs_));
		}
	}
	
	function onError($exception) {
		
	}
	
	/**
	 * Start the thread
	 *
	 * @return void
	 * @author sparkwang
	 */
	function start() {
		$this->proxy = new ThreadProxy($this);
		$sockets = array();
		if (!$sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP)) {
			throw new Exception("Thread: stream_socket_pair failed!");
		}
		pcntl_signal(SIGCHLD, SIG_IGN);
		$pid = pcntl_fork();
		if ($pid == 0) {
			$this->child_ = true;
			fclose($sockets[0]);
			$this->handle_ = $sockets[1];
			stream_set_blocking($this->handle_, 0);
			$this->transport_ = new Transport($this->handle_);
			$this->eventbase_ = event_base_new();
			$this->socketEvent_ = event_new();
			event_set($this->socketEvent_, $this->handle_, EV_READ | EV_PERSIST, array($this, 'onCall'));
			event_base_set($this->socketEvent_, $this->eventbase_);
			event_add($this->socketEvent_);
			event_base_loop($this->eventbase_);
		} else if ($pid > 0) {
			$this->pid_ = $pid;
			fclose($sockets[1]);
			$this->handle_ = $sockets[0];
			stream_set_blocking($this->handle_, 0);
			$this->transport_ = new Transport($this->handle_);
			$this->eventbase_ = $this->eventbase_ ? $this->eventbase_ : event_base_new();
			$this->socketEvent_ = event_new();
			event_set($this->socketEvent_, $this->handle_, EV_READ | EV_PERSIST, array($this, 'onCall'));
			event_base_set($this->socketEvent_, $this->eventbase_);
			event_add($this->socketEvent_);
		} else {
			throw new Exception("Thread: Could not do fork!");
		}
	}
	
	/**
	 * Stop the thread
	 *
	 * @return void
	 * @author sparkwang
	 */
	function stop() {
		if ($this->child_) {
			posix_kill(getmypid(), SIGTERM);
			exit();
		} else {
			$this->rpc('stop');
		}
	}
	
	/**
	 * Wait the call back for sync-process
	 *
	 * @return void
	 * @author sparkwang
	 */
	function waitForCallback_() {
		if (!$this->callbackflag_) return;
		$time = microtime(true);
		while($this->callbackflag_ && (microtime(true) - $time)*1000 < $this->callbacktimeout_) {}
		$this->callbackflag_ = false;
	}
}

/**
 * Proxy to the child/parent, using the php magic method
 *
 * @package phprpc
 * @subpackage libs
 * @author sparkwang
 */
class ThreadProxy {
	private $thread_ = null;
	
	function __construct($thread) {
		$this->thread_ = $thread;
	}
	
	function __call($method, $args) {
		$this->thread_->rpc($method, $args, $this->thread_->context, $this->thread_->callback);
	}
}
?>