<?php
/* SVN FILE: $Id: class_registry.php 1462 2010-05-12 09:31:01Z sparkwang $ */
/**
 * Class collections.
 *
 * A repository for class objects, each registered with a key.
 *
 * PHP versions 4 and 5
 *
 * @package			cola
 * @subpackage		cola.core.libs
 * @since			ColaPHP(tm) v 0.9.2
 * @version			$Revision: 1462 $
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$Date: 2010-05-12 17:31:01 +0800 (三, 2010-05-12) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Class Collections.
 *
 * A repository for class objects, each registered with a key.
 * If you try to add an object with the same key twice, nothing will come of it.
 * If you need a second instance of an object, give it another key.
 *
 * @package		cola
 * @subpackage	cola.core.libs
 */
class ClassRegistry{
/**
 * Names of classes with their objects.
 *
 * @var array
 * @access private
 */
	var $_objects = array();
/**
 * Return a singleton instance of the ClassRegistry.
 *
 * @return ClassRegistry instance
 */
	static function &getInstance() {
		static $instance = array();
		if (!$instance) {
			$instance[0] = new ClassRegistry;
		}
		return $instance[0];
	}
/**
 * Add $object to the registry, associating it with the name $key.
 *
 * @param string $key
 * @param mixed $object
 */
	static function addObject($key, &$object) {
		$_this =& ClassRegistry::getInstance();
		$key = strtolower($key);
		if (array_key_exists($key, $_this->_objects) === false) {
			$_this->_objects[$key] = &$object;
		}
	}
/**
 * Remove object which corresponds to given key.
 *
 * @param string $key
 * @return void
 */
	function removeObject($key) {
		$_this =& ClassRegistry::getInstance();
		$key = strtolower($key);
		if (array_key_exists($key, $_this->_objects) === true) {
			unset($_this->_objects[$key]);
		}
	}
/**
 * Returns true if given key is present in the ClassRegistry.
 *
 * @param string $key Key to look for
 * @return boolean Success
 */
	static function isKeySet($key) {
		$_this =& ClassRegistry::getInstance();
		$key = strtolower($key);
		return array_key_exists($key, $_this->_objects);
	}
/**
 * Get all keys from the regisrty.
 *
 * @return array
 */
	static function keys() {
		$_this =& ClassRegistry::getInstance();
		return array_keys($_this->_objects);
	}
/**
 * Return object which corresponds to given key.
 *
 * @param string $key
 * @return mixed
 */
	static function &getObject($key) {
		$_this =& ClassRegistry::getInstance();
		$key = strtolower($key);
		return $_this->_objects[$key];
	}
}
?>