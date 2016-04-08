<?php
/* SVN FILE:: $Id: thread.php 3777 2010-09-21 03:04:02Z sparkwang $ */
/**
 * Class wrapper php fork with IPC
 *
 * @package
 * @subpackage
 * @copyright 		Copyright (c) 2010 tencent
 * @author			sparkwang
 * @version			$LastChangedRevision: 3777 $
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$LastChangedDate: 2010-09-21 11:04:02 +0800 (二, 2010-09-21) $
 */

class Thread {
	
	private $handle_ = null;
	private $eventbase_ = null;
	private $socketEvent_ = null;
	private $pid_ = 0;
	private $child_ = false;
	private $buf_ = '';
  private $sendTimeout_ = 5000;
  private $recvTimeout_ = 750;
  private $sendTimeoutSet_ = TRUE;
  private $callbacktimeout_ = 750;
  private $callbackflag_ = false;
  
	public $context = array();	
  public $callback = false;
	public $proxy = null;
	
	function Thread() {
		$this->proxy = new ThreadProxy($this);
	}
	
	function setEventBase($base) {
		$this->eventbase_ = $base;
	}
	
	function getPid() {
		return $this->pid_;
	}
	
	function start() {
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
	  	$this->eventbase_ = $this->eventbase_ ? $this->eventbase_ : event_base_new();
			$this->socketEvent_ = event_new();
			event_set($this->socketEvent_, $this->handle_, EV_READ | EV_PERSIST, array($this, 'onCall'));
			event_base_set($this->socketEvent_, $this->eventbase_);
			event_add($this->socketEvent_);
    } else {
    	throw new Exception("Thread: Could not do fork!");
    }
	}
	
	function isBusy() {
		return $this->callback && $this->callbackflag_;
	}
	
  private function read_($len) {
    if ($this->sendTimeoutSet_) {
      stream_set_timeout($this->handle_, 0, $this->recvTimeout_*1000);
      $this->sendTimeoutSet_ = FALSE;
    }
    $pre = null;
    while (TRUE) {
      $buf = fread($this->handle_, $len);
      if ($buf === FALSE || $buf === '') {
        $md = stream_get_meta_data($this->handle_);
        if ($md['timed_out']) {
          throw new Exception('Thread: timed out reading '.$len.' bytes from ');
        } else {
          throw new Exception('Thread: Could not read '.$len.' bytes from ');
        }
      } else if (($sz = strlen($buf)) < $len) {
        $md = stream_get_meta_data($this->handle_);
        if ($md['timed_out']) {
          throw new Exception('Thread: timed out reading '.$len.' bytes from ');
        } else {
          $pre .= $buf;
          $len -= $sz;
        }
      } else {
        return $pre.$buf;
      }
    }
  }

  private function write_($buf, $len = null) {
  	stream_set_blocking($this->handle_, 1);
    while (strlen($buf) > 0) {
      $got = @fwrite($this->handle_, $buf, $len ? $len : strlen($buf));
      if ($got === 0 || $got === FALSE) {
        $md = stream_get_meta_data($this->handle_);
        if ($md['timed_out']) {
          throw new Exception('Thread: timed out writing '.strlen($buf));
        }
      }
      $buf = substr($buf, $got);
    }
    stream_set_blocking($this->handle_, 0);
  }

  private function flush_() {
    $ret = fflush($this->handle_);
  }
	
	public function onCall() {
		$this->callbackflag_ = false;
		$len = $this->read_(4);
		$len = unpack('N', $len);
    $len = $len[1];
    if ($len > 0x7fffffff) {
      $len = 0 - (($len - 1) ^ 0xffffffff);
    }
		if (!$len) return;
		if ($data = $this->read_($len)) {
			$call = unserialize($data);
			list($method, $args, $context) = $call;
			try {
				if (!empty($context)) {
					$this->context = array_merge($this->context, $context);
				}
				$ret = call_user_func_array(array($this, $method), $args);
			} catch (Exception $e){
				$this->rpc('onError', array($e));
			}
		}
	}
	
	public function onError($exception) {
	}
	
	function rpc($method, $args = array(), $context = array()) {
		if ($this->callback) {
			$this->waitForCallback_();
		}
		$this->callbackflag_ = true;
		$call = array($method, $args, $context);
		$call = serialize($call);
		$count = strlen($call);
		$this->write_(pack('N', $count) . $call);
	}
	
	function waitForCallback_() {
		if (!$this->callbackflag_) return;
		$time = microtime(true);
		while($this->callbackflag_ && (microtime(true) - $time)*1000 < $this->callbacktimeout_) {}
		$this->callbackflag_ = false;
	}
	
	function loop($block_once = false) {
		if ($block_once) {
			event_base_loop($this->eventbase_, EVLOOP_ONCE);
		} else {
			event_base_loop($this->eventbase_, EVLOOP_NONBLOCK);
		}
	}
	
	function stop() {
		if ($this->child_) {
			posix_kill(getmypid(), SIGTERM);
			exit();
		} else {
			$this->rpc('stop');
		}
	}
	
}

class ThreadProxy {
	private $thread_ = null;
	public $context = array();
	
	function ThreadProxy($thread) {
		$this->thread_ = $thread;
	}
	
	function __call($method, $args) {
		$this->thread_->rpc($method, $args, $this->context);
	}
}
?>