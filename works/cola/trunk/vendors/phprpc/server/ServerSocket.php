<?php
/* SVN FILE:: $Id: ServerSocket.php 4360 2010-11-01 07:37:24Z sparkwang $ */
/**
 * Rpc server socket class
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
 * Rpc server socket
 *
 * @package phprpc
 * @subpackage server
 * @author sparkwang
 */
class ServerSocket {	
	protected $host_ = '0.0.0.0';
	protected $port_ = 0;
	protected $handle_;
	protected $errno_ = 0;
	protected $errstr_ = '';
	protected $base_;
	protected $serverEvent_;
	protected $callback_;
	
	function __construct($host, $port = 9090) {
		$this->host_ = $host;
		$this->port_ = $port;
		$this->base_ = EventBase::getBase();
	}
	
	/**
	 * Set call back on request
	 *
	 * @param mixed $callback 
	 * @return void
	 * @author sparkwang
	 */
	function setCallBack($callback) {
		$this->callback_ = $callback;
	}
	
	function listen($loop = false) {
		$this->handle_ = stream_socket_server("tcp://{$this->host_}:{$this->port_}", $this->errno_, $this->errstr_);
		stream_set_blocking($this->handle_, 0);		
		$this->serverEvent_ = event_new();
		event_set($this->serverEvent_, $this->handle_, EV_READ | EV_PERSIST, array($this, 'onConnect_'));
		event_base_set($this->serverEvent_, $this->base_);
		event_add($this->serverEvent_);
	}
	
	function onConnect_() {
		$clientSocket = stream_socket_accept($this->handle_);
		stream_set_blocking($clientSocket, 0);
		$clientEvent = event_new();
		event_set($clientEvent, $clientSocket, EV_READ | EV_PERSIST, array($this, 'onRequest_') , array($clientEvent));
		event_base_set($clientEvent, $this->base_);
		event_add($clientEvent);
	}
	
	function onRequest_($clientSocket, $events, $arg) {
		try {
			$transport = new Transport($clientSocket);
			call_user_func($this->callback_, $transport);
		} catch(Exception $e) {
			event_del($arg[0]);
			event_free($arg[0]);
			@stream_socket_shutdown($clientSocket, STREAM_SHUT_RDWR);
			@fclose($clientSocket);
			return;
		}
	}
}

?>
