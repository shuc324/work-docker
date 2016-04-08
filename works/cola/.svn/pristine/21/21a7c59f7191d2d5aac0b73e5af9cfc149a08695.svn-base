<?php
/* SVN FILE:: $Id: basics.php 4662 2010-11-18 06:36:04Z lightma $ */
/**
 * file comments here ...
 *
 * @package			
 * @subpackage		
 * @copyright 		Copyright (c) 2010 tencent
 * @author			sparkwang
 * @version			$LastChangedRevision: 4662 $ 
 * @modifiedby		$LastChangedBy: lightma $
 * @lastmodified	$LastChangedDate: 2010-11-18 14:36:04 +0800 (四, 2010-11-18) $
 */

if (!defined('DEBUG')) {    
    $env_debug = getenv('DEBUG');
    if ($env_debug === false) {
        define('DEBUG', 1);
    } elseif ($env_debug <= 0) {
		define('DEBUG', 0);
	} else {        
		define('DEBUG', $env_debug);
	}
}
if (!defined('CSIS_HOST')) {
	if (DEBUG) {
		define('CSIS_HOST', '10.7.12.20');
	} else {
		define('CSIS_HOST', 'csis.cm.com');
	}
}
if (!defined('CSIS_HOST_PORT')) {
	define('CSIS_HOST_PORT', '9090');
}
if (!defined('CSIS_URI')) {
	if (DEBUG) {
		define('CSIS_URI', 'http://10.7.12.20/csis/services');
	} else {
		define('CSIS_URI', 'http://csis.cm.com/services');
	}
}

if (!defined('CSIS_SYSTEM')) {
	define('CSIS_SYSTEM', 'NONE_SYSTEM');
}

if (!defined('CSIS_PASSWORD')) {
	define('CSIS_PASSWORD', '');
}

if (!defined('CSIS_PASSWORD')) {
	define('CSIS_PASSWORD', '');
}

define('CSIS_PROTOCOL_SOAP', 'soap');
define('CSIS_PROTOCOL_GET', 'get');
define('CSIS_PROTOCOL_POST', 'post');
define('CSIS_PROTOCOL_REST', 'rest');
define('CSIS_PROTOCOL_SOCKET', 'socket');
if (!defined('CSIS_PROTOCOL')) {
	define('CSIS_PROTOCOL', CSIS_PROTOCOL_SOAP);
}

if (!defined('LOGS')) {
	if (defined('TMP')) {
		define('LOGS', TMP);
	} else {
		define('LOGS', '/tmp');
	}
}

/**
 * function to request the csis service
 *
 * @param string $service service name to request
 * @param string $method service method to reqest
 * @param mixed $params service method params
 * @param string $system local system name that register on csis
 * @param string $password local system password that register on csis
 * @param string $protocol soap/(get/post)rest/, const CSIS_PROTOCOL_SOAP/CSIS_PROTOCOL_REST/CSIS_PROTOCOL_GET/CSIS_PROTOCOL_POST, default soap
 * @return mixed
 */
function csis_request($service, $method, $params, $system = null, $password = null, $protocol = CSIS_PROTOCOL) {
    $system = empty($system) ? CSIS_SYSTEM : $system;
    $password = empty($password) ? CSIS_PASSWORD : $password;
    $ret = null;
	if ($protocol == CSIS_PROTOCOL_REST || $protocol == CSIS_PROTOCOL_GET) {
		$ret = _csis_request_get($service, $method, $params, $system, $password);
	} else if ($protocol == CSIS_PROTOCOL_POST) {
		$ret = _csis_request_post($service, $method, $params, $system, $password);
	} else if ($protocol == CSIS_PROTOCOL_SOAP) {
		$ret = _csis_request_soap($service, $method, $params, $system, $password);
	} else if ($protocol == CSIS_PROTOCOL_SOCKET) {
		$ret = _csis_request_socket($service, $method, $params, $system, $password);
	}
	
	return $ret;
}

/**
 * request service via url get
 *
 * @param string $service service name to request
 * @param string $method service method to reqest
 * @param mixed $params service method params
 * @param string $system local system name that register on csis
 * @param string $password local system password that register on csis
 * @return mixed
 */
function _csis_request_get($service, $method, $params, $system = null, $password = null) {
    $system = empty($system) ? CSIS_SYSTEM : $system;
    $password = empty($password) ? CSIS_PASSWORD : $password;
    $address = CSIS_URI . '/rest/' . $service . '/' . $method;
	
    if ($params) {
    	if (!is_array($params)) $params = array($params);
		array_walk_recursive($params, '__null_to_empty');
    	$address .= '?' . http_build_query($params);
    }
	
    if (strlen($address) > 1000){
        $p = strpos($address, '?');
        if ($p !== false) {
            $post_fields = substr($address, $p+1);
            $address = substr($address, 0, $p);           
        }
    }

    $ch = curl_init($address);
    if (isset($post_fields)){			
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_NOBODY, 0); 			
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "$system:$password");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	
    $ret = curl_exec($ch);
	if ($ret === false)
	{
		throw new Exception('curl exec error');
	}
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $code = (int)$code;
    if ($code >= 400) {
    	throw new Exception($ret);
    }

    $ret = json_decode($ret);
    return $ret;
}

/**
 * request for soap
 * 
 * @param string $service service name to request
 * @param string $method service method to reqest
 * @param mixed $params service method params
 * @param string $system local system name that register on csis
 * @param string $password local system password that register on csis
 * @return mixed
 */
function _csis_request_soap($service, $method, $params = array(), $system = null, $password = null) {
    $system = empty($system) ? CSIS_SYSTEM : $system;
    $password = empty($password) ? CSIS_PASSWORD : $password;
    $address = CSIS_URI . '/soap?class=' . $service . '&wsdl';
    ini_set("soap.wsdl_cache_enabled", "0");
    $mySoap = new SoapClient($address, array('login' => $system, 'password' => $password));
    $ret = $mySoap->__soapCall($method, $params ? (is_array($params) ? $params : array_values(get_object_vars($params))) : array());
    return $ret;
}

/**
 * request for post
 * 
 * @param string $service service name to request
 * @param string $method service method to reqest
 * @param mixed $params service method params
 * @param string $system local system name that register on csis
 * @param string $password local system password that register on csis
 * @return mixed
 */
function _csis_request_post($service, $method, $params = array(), $system = null, $password = null) {
    $system = empty($system) ? CSIS_SYSTEM : $system;
    $password = empty($password) ? CSIS_PASSWORD : $password;
	$address = CSIS_URI . '/rest/' . $service . '/' . $method;
	if (is_array($params))
	{
		array_walk_recursive($params, '__null_to_empty');
		$fields = http_build_query($params);
	}
	else
	{
		$fields = $params;
	}
    
    $ch = curl_init($address);
    if (!empty($params)) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "$system:$password");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	
    $ret = curl_exec($ch);
	if ($ret === false)
	{
		throw new Exception('curl exec error');
	}
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $code = (int)$code;
    if ($code >= 400) {
    	throw new Exception($ret);
    }

    $ret = json_decode($ret);
    return $ret;
}

/**
 * request for socket
 * 
 * @param string $service service name to request
 * @param string $method service method to reqest
 * @param mixed $params service method params
 * @param string $system local system name that register on csis
 * @param string $password local system password that register on csis
 * @return mixed
 */
function _csis_request_socket($service, $method, $params = array(), $system = null, $password = null) {
	$params['__system'] = $system;
	$params['__password'] = $password;
	
	require_once 'thrift.php';	
	error_reporting(0);
	require_once 'csis/libs/thrift/gen-php/csis_service/csis_service_types.php';
	require_once 'csis/libs/thrift/gen-php/csis_service/CsisService.php';
	error_reporting(E_ALL);	
	
	static $client = null;
	if (!$client) {
		$socket = new TSocket(CSIS_HOST, CSIS_HOST_PORT);
		$socket->setRecvTimeout(8000);
		$transport = new TBufferedTransport($socket, 1024, 1024);
		$protocol = new TBinaryProtocol($transport);
		$client = new CsisServiceClient($protocol);
		$transport->open();
	}
	
	$ret = $client->request($service, $method, json_encode($params));
	$ret = json_decode($ret);
	
	return $ret;
}
/**
 * 将数组中为null的值设置成空字符串，用于http_build_query
 */
function __null_to_empty(&$value, $key)
{
	if ($value === null)
	{
		$value = '';
	}
}
?>