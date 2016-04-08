<?php
/* SVN FILE:: $Id: csis.php 1760 2010-06-02 08:10:10Z sparkwang $ */
/**
 * file comments here ...
 *
 * @package
 * @subpackage
 * @copyright 		Copyright (c) 2010 tencent
 * @author			sparkwang
 * @version			$LastChangedRevision: 1760 $
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$LastChangedDate: 2010-06-02 16:10:10 +0800 (三, 2010-06-02) $
 */

require_once('csis/libs/basics.php');

/**
 * request the csis service
 *
 * Usage:
 * <code>
 * 	$service = new CsisService('service_name');
 *  $result = $service->method_name(array('method_argvs'));
 * </code>
 * @package Csis
 */
class CsisService {
	var $service = null;
	var $method = null;
	var $protocol = CSIS_PROTOCOL_SOAP;
	var $log_failed_file = null;

	/**
	 * constructor of the CsisService
	 *
	 * @param string $service service name of csis
	 * @param string $protocol soap/(get/post)rest/, const CSIS_PROTOCOL_SOAP/CSIS_PROTOCOL_REST/CSIS_PROTOCOL_GET/CSIS_PROTOCOL_POST, default soap
	 */
	function __construct($service = null, $protocol = CSIS_PROTOCOL_SOAP) {
		$this->service = $service;
		$this->protocol = $protocol;
		$this->logfailed();
	}
	
	function logfailed($file = null) {
		if (!empty($file)) {
			$this->log_failed_file = $file;
		} else {
			$this->log_failed_file = LOGS . DIRECTORY_SEPARATOR . 'csis_failed_' . date('Ymd') . '.log';
		}
	}

	/**
	 * do request of some service
	 *
	 * @param string $service service name of csis
	 * @param string $method service method
	 * @param mixed $params parameters array, or one simple type parameter
	 * @return mixed
	 */
	function &request($service, $method, $params) {
		$ret = null;
		try {
			$ret = csis_request($service, $method, $params);
		} catch (Exception $e) {
			require_once('log.php');
			$logger = &Log::singleton('file', $this->log_failed_file, 'Csis');
			
			$data = array('service' => $service, 'method' => $method, 'params' => $params, '__system' => CSIS_SYSTEM, '__password' => CSIS_PASSWORD, '__error' => $e->getMessage());
			$data = json_encode($data);
			try {
				$logger->log($data, PEAR_LOG_ERR);
			}catch(Exception $e1){}
			
			throw $e;
		}
		return $ret;
	}
	
	/**
	 * invoke service method
	 *
	 * @param string $method
	 * @param mixed $params
	 * @return mixed
	 */
	function invoke($method, $params) {
		$ret = $this->request($this->service, $method, $params);
		return $ret;
	}

	function __call($method, $params) {
		if ($method != 'request' && $method != 'invoke') {
			$ret = $this->invoke($method, $params);
			return $ret;
		}
		$ret = parent::__call($method, $params);
		return $ret;
	}
}
?>