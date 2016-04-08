<?php
/* SVN FILE:: $Id: apis_controller_base.php 7754 2011-07-22 09:16:38Z sparkwang $ */

/**
 * Apis controller class.
 *
 * PHP versions 4 and 5
 *
 * @package			cola
 * @subpackage		cola.core.libs.controller
 * @since			ColaPHP(tm) v 0.2.9
 * @version			$Revision: 7754 $
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$Date: 2011-07-22 17:16:38 +0800 (五, 2011-07-22) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
 
/**
 * ApisController
 *
 * Application apis controller
 *
 * @package		cola
 * @subpackage	cola.core.libs.controller
 *
 */
class ApisControllerBase extends Controller{
	var $uses = array();
	var $docTemplate = null;
	var $apiBase = null;
	
	function index() {
		if (substr($this->here, -1) != '/') $this->redirect($this->params['controller'] . '/');
		$services = loadServices();
		vendor('wsdl/documentation');
		$phpdoc = new IPPhpdoc();
		if (isset($_GET['class'])) {
			if (substr($_SERVER['QUERY_STRING'], -4) == 'wsdl') {
				$this->setAction('soap');
				exit();
			} else {
				$phpdoc->setClass($_GET['class']);
			}
		}
		
		if(isset($_GET['class']) || $_GET['show']=='dreamix')
		{
			$phpdoc->setClasses(array_values($services));
			echo $phpdoc->getDocumentation($this->docTemplate ? $this->docTemplate : VENDORS . '/wsdl/templates/default.xsl');
		}
		exit();
	}
	
	function soap() {
		$wsdl = substr($_SERVER['QUERY_STRING'], -4) == 'wsdl';
		ini_set("soap.wsdl_cache_enabled", "0");
		
		$serviceName = $_GET['class'];
		$className = Inflector::camelize($serviceName);
		
		vendor('wsdl');
		$wshelper = new WSHelper("http://kf.cm.com", $className);

		try {
			if (!$this->access($className, null)) {
				throw new ApiException("Unauthorized!", ApiException::EX_REQUEST_UNAUTHORIZED);
			}
			$ret = loadService($className);
			if (!$ret) {
				throw new ApiException("Service or method not found!", ApiException::EX_SERVER_NOTFOUND);
			}
			
			$wshelper->actor = "http://kf.cm.com/schema";
			$wshelper->base = (isset($_SERVER['HTTPS'])?'https://':'http://') . $_SERVER['HTTP_HOST'] . ($_SERVER['SERVER_PORT'] == 80 ? '' : ':' . $_SERVER['SERVER_PORT']) . $this->webroot . $this->params['controller'] . '/' . $this->action;
			$wshelper->use = SOAP_ENCODED;
			$wshelper->classNameArr = array($className);
			$wshelper->setPersistence(SOAP_PERSISTENCE_REQUEST);
			$wshelper->useWSDLCache = false;
			$wshelper->setWSDLCacheFolder(APP.'tmp/cache/persistents/');
			
			$wshelper->handle($wsdl);
			
		}catch(Exception $e) {
			$wshelper->fault($e->getCode(), $e->getMessage());
		}
		exit();
	}
	
	function rest($serviceName = null, $method = null, $retType = 'json') {
		try {
			$ret = $this->__rest($serviceName, $method);
		} catch (Exception $e) {
			$ret = $e;
		}
		
		if ($retType != 'xml') {
			$retType = 'json';
		}
		
		$this->__rest_out($ret, $retType);
	}
	
	function __rest($serviceName = null, $method = null) {
		$className = Inflector::camelize($serviceName);
		if (!$this->access($className, $method)) {
			throw new ApiException("Unauthorized!", ApiException::EX_REQUEST_UNAUTHORIZED);
		}
		$service = loadService($className, true);
		if (!$service) {
			throw new ApiException("Service or method not found!", ApiException::EX_SERVER_NOTFOUND);
		}
		
		$params = $this->params['url'];
		unset($params['url']);
		$params = array_merge($params, $this->params['form']);
		
		$m = new ReflectionMethod($service, $method);
		if (!$m->isPublic()) {
			throw new ApiException("Service or method not found!", ApiException::EX_SERVER_NOTFOUND);
		}
		
		$args = array();
		$ps = $m->getParameters();
		foreach ($ps as $p) {
			$args[] = @$params[$p->getName()];
		}
		
		$ret = call_user_func_array(array($service, $method), $args);		
		return $ret;
	}
	
	function __rest_out($out, $retType = 'json') {
		if ($out instanceof Exception) {
			$status = $out instanceof ApiException ? $out->getCode() : 500;
			header("HTTP/1.1 $status Error");
			$out = array('Exception' => array('Status' => $status, 'Code' => $out->getCode(), 'Message' => $out->getMessage()));
		}
		if ($retType == 'xml') {
			header('Content-Type: application/xml');
			echo $this->___xml_serializer($out);
		} else if ($retType == 'json') {
			header('Content-Type: application/json');
			$out = json_encode($out);
			if (isset($this->params['url']['callback'])) {
				echo "var _callbackvar=$out;" . $this->params['url']['callback'] . "(_callbackvar);";
			} else {
				echo $out;
			}
		}
		exit();
	}

	function ___xml_serializer($obj) {
		vendor('XML_Serializer' . DS . 'Serializer');
		$serializer_options = array (
		   'addDecl' => true,
		   'encoding' => 'UTF-8',
		   'indent' => '  ',
		   'rootName' => 'ApiResponse',
		   'defaultTagName' => 'item',
		);

		$Serializer = new XML_Serializer($serializer_options);
		$status = $Serializer->serialize($obj);

		if ($status) {
			return $Serializer->getSerializedData();
		}
		return null;
	}
	
	function beforeFilter() {
		set_time_limit(10);
		if (in_array(strtolower($this->params['action']), array('access', 'excludes'))) {
			exit();
		}
	}
	
	function access($serviceName, $method) {
		return true;
	}
	
	function excludes() {
		return null;
	}
}

class ApiException extends Exception {
	const EX_REQUEST = 400;
	const EX_REQUEST_UNAUTHORIZED = 401;
	const EX_SERVER = 500;
	const EX_SERVER_NOTFOUND = 501;	
	const EX_REQUEST_INVAL = 601;
	//user defined Exception must be bigger than 700
	
	var $_message = array(
		ApiException::EX_REQUEST => "Bad request",
		ApiException::EX_REQUEST_UNAUTHORIZED => 'Unauthorized',
		ApiException::EX_SERVER => 'Internal Server Error',
		ApiException::EX_SERVER_NOTFOUND => 'Service not found',
		ApiException::EX_REQUEST_INVAL => 'Invalid argument',		
	);
	function &singleton() {
		static $api_exception;
		if(!isset($api_exception)) {
			$api_exception = new ApiException();
		}
		return $api_exception;
	}
	function message($code) {
		$api_exception = ApiException::singleton();
		if(key_exists($code, $api_exception->_message)) {
			return $api_exception->_message[$code];
		} else {
			return false;
		}
	}
}
	
?>