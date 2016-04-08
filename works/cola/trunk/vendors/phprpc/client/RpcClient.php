<?php
/* SVN FILE:: $Id: RpcClient.php 4360 2010-11-01 07:37:24Z sparkwang $ */
/**
 * Rpc client class
 *
 * @package			phprpc
 * @subpackage		client
 * @copyright 		Copyright (c) 2010 tencent
 * @author			sparkwang
 * @version			$LastChangedRevision: 4360 $ 
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$LastChangedDate: 2010-11-01 15:37:24 +0800 (一, 2010-11-01) $
 */

/**
 * Rpc client
 *
 * @package phprpc
 * @subpackage client
 * @author sparkwang
 */
class RpcClient {
	protected $socket_;
	protected $transport_;
	protected $service_;
	
	/**
	 * Rpc client constructor
	 *
	 * @param string $host Rpc server host
	 * @param int $port Rpc server port
	 * @param string $service Service to request
	 * @author sparkwang
	 */
	function __construct($host = 'localhost', $port = 9090, $service = null) {
		if (func_num_args() == 1 && preg_match('/tcp\:\/\/(.*?):(\d+)\/(.*)$/', $host, $matches)) {
			$this->host_ = $matches[1];
			$this->port_ = $matches[2];
			$this->service_ = $matches[3];
		} else {
			$this->host_ = $host;
			$this->port_= $port;
			$this->service_ = $service;
		}
		$this->socket_ = stream_socket_client("tcp://{$this->host_}:{$this->port_}", $errno, $errstr);
		$this->transport_ = new Transport($this->socket_);
	}
	
	/**
	 * Call remote service/method. This method is more like the php call_user_func_array function
	 *
	 * @param string $method The method to be called. 
	 * @param mixed $args The arguments to be passed to the method, as an indexed array.
	 * @return mixed
	 * @author sparkwang
	 */
	function __call($method, $args) {
		$request = new JsonRequest(!empty($this->service_) && is_string($method) ? array($this->service_, $method) : $method, $args, JsonRequest::NEXTID);
		$this->transport_->writeAll($request->toJson());
		$response = $this->transport_->readAll();;
		$response = JsonResponse::decode($response);
		if ($response->error) {
			throw new Exception($response->error);
		}
		return $response->result;
	}
}
?>
