<?php
/* SVN FILE:: $Id: RpcServer.php 4360 2010-11-01 07:37:24Z sparkwang $ */
/**
 * Rpc server main class
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
 * Rpc server
 *
 * @package phprpc
 * @subpackage server
 * @author sparkwang
 */
class RpcServer {
	protected $host_;
	protected $port_;
	protected $socket_;
	protected $processor_ = null;
	protected $processors_ = array();
	protected $services_ = array();
	
	/**
	 * RpcServer constructor
	 *
	 * @param string $host Host to bind
	 * @param int $port Port to bind
	 * @param mixed $services Services to publish
	 * @author sparkwang
	 */
	function __construct($host, $port, $services = null) {
		$this->socket_ = new ServerSocket($host, $port);
		$this->socket_->setCallBack(array($this, 'request_'));
		$services = is_array($services) ? $services : array($services);
		foreach ($services as $service) {
			$this->addService($service);
		}
	}
	
	/**
	 * Start the rpc server
	 *
	 * @param int $threads threads count, default 0 means not use thread
	 * @return void
	 * @author sparkwang
	 */
	function start($threads = 0) {
		for ($i = 0; $i < $threads; $i++) {
			$processor = new RpcProcessor($this->services_);
			$processor->setEventBase(EventBase::getBase());
			$processor->start();
			$this->processors_[] = $processor;
		}
		$this->socket_->listen();
		$sigevent = event_new();
		event_set($sigevent, SIGTERM, EV_SIGNAL | EV_PERSIST, array($this, 'sigHandler_'), array(SIGTERM));
		event_base_set($sigevent, EventBase::getBase());
		event_add($sigevent);
		EventBase::loop();
	}

	function sigHandler_($fd, $events, $args) {
		switch ($args[0]) {
			case SIGTERM:
			case SIGINT:
				foreach ($this->processors_ as $processor) {
					$processor->stop();
				}
				exit();
		}
	}
	
	/**
	 * Add service/function to rpc server
	 *
	 * @param mixed $service A class/instance/function
	 * @return void
	 * @author sparkwang
	 */
	function addService($service) {
		if (is_object($service)) {
			$this->services_[get_class($service)] = $service;
		} else if (class_exists($service)) {
			$this->services_[$service] = new $service;
		} else if (function_exists($service)) {
			$this->services_[$service] = $service;
		}
	}
	
	function request_($transport) {
		$request = $transport->readAll();
		if (count($this->processors_) == 0) {
			if (!$this->processor_) $this->processor_ = new RpcProcessor($this->services_);
			return $this->response_($this->processor_->process($request), $transport);
		}
		while (true) {
			foreach ($this->processors_ as $processor) {
				if ($processor->isBusy()) continue;
				$processor->setCallBack(array($this, 'response_'), $transport);
				$processor->proxy->process($request);
				return;
			}
		}
	}
	
	function response_($result, $transport) {
		$transport->writeAll($result->toJson());
	}
}

?>
