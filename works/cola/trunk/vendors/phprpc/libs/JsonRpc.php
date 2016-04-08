<?php
/* SVN FILE:: $Id: JsonRpc.php 4360 2010-11-01 07:37:24Z sparkwang $ */
/**
 * Json rpc message classes
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
 * Json rpc message
 *
 * @package phprpc
 * @subpackage libs
 * @author sparkwang
 */
class JsonMessage {
	static function encode($message) {
		return json_encode($message);
	}
	static function decode($message) {
		return json_decode($message);
	}
	
	function toJson() {
		return self::encode($this);
	}
}

/**
 * Json rpc request message
 *
 * @package phprpc
 * @subpackage libs
 * @author sparkwang
 */
class JsonRequest extends JsonMessage {
	public $method;
	public $params;
	public $id;
	const NEXTID = -1;
	
	function __construct($method, $params, $id = null) {
		$this->method = $method;
		$this->params = $params;
		$this->id = $id == self::NEXTID ? self::nextId() : $id;
	}
	
	public static function nextId() {
		static $id = 0;
		return ++$id;
	}
}

/**
 * Json rpc response message
 *
 * @package phprpc
 * @subpackage libs
 * @author sparkwang
 */
class JsonResponse  extends JsonMessage {
	public $result;
	public $error;
	public $id;
	
	function __construct($result, $error, $id = null) {
		$this->result = $result;
		$this->error = $error;
		$this->id = $id;
	}
}

?>
