<?php
/* SVN FILE:: $Id: RpcProcessor.php 4360 2010-11-01 07:37:24Z sparkwang $ */
/**
 * Rpc-request processor class
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
 * Rpc-request processor
 *
 * @package phprpc
 * @subpackage server
 * @author sparkwang
 */
class RpcProcessor extends Thread {
	protected $services_ = array();
	
	/**
	 * RpcProcessor constructor
	 *
	 * @param mixed $services Services to process. See RpcServer
	 * @author sparkwang
	 */
	function __construct($services) {
		$this->services_ = $services;
	}
	
	/**
	 * Process request
	 *
	 * @param JsonRequest $request JsonRequest message
	 * @return void
	 * @author sparkwang
	 */
	function process($request) {
		$request = JsonRequest::decode($request);
		$method = $request->method;
		$key = null;
		if (is_array($method)) {
			list($key) = $method;
		} else if (is_string($method)) {
			$key = $method;
		}
		try {
			if (!isset($this->services_[$key])) throw new Exception("Unkown method {@$request->method}");			
			$result = call_user_func_array($method, @$request->params);
			$result = new JsonResponse($result, null, $request->id);
			return $result;
		} catch (Exception $e) {
			$result = new JsonResponse(null, $e->getMessage(), $request->id);
			return $result;
		}
	}
}

?>
