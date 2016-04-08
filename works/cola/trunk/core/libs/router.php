<?php
/* SVN FILE: $Id: router.php 8572 2011-08-31 09:42:49Z firoyang $ */
/**
 * Parses the request URL into controller, action, and parameters.
 *
 * PHP versions 4 and 5
 *
 * @package			cola
 * @subpackage		cola.core.libs
 * @since			ColaPHP(tm) v 0.2.9
 * @version			$Revision: 8572 $
 * @modifiedby		$LastChangedBy: firoyang $
 * @lastmodified	$Date: 2011-08-31 17:42:49 +0800 (三, 2011-08-31) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Included libraries.
 *
 */
	if (!class_exists('Object')) {
		 uses ('object');
	}
/**
 * Parses the request URL into controller, action, and parameters.
 *
 * @package		cola
 * @subpackage	cola.core.libs
 */
class Router extends Object {
/**
 * Array of routes
 *
 * @var array
 * @access public
 */
	 var $routes = array();
/**
 * COLA_ADMIN route
 *
 * @var array
 * @access private
 */
	 var $__admin = null;
/**
 * default route
 *
 * @var array
 * @access public
 */
	 var $__default_route = array('/:controller/:action/* (default)',
								'/^(?:\/(?:([a-zA-Z0-9_\\-\\.\\;\\:]+)(?:\\/([a-zA-Z0-9_\\-\\.\\;\\:]+)(?:[\\/\\?](.*))?)?))[\\/]*$/',
								array('controller', 'action'), array());
/**
 * Constructor
 *
 * @access public
 */
	 function __construct() {
		  if (defined('COLA_ADMIN')) {
			   $admin = COLA_ADMIN;
			   if (!empty($admin)) {
					$this->__admin = array('/:' . $admin . '/:controller/:action/* (default)',
										   '/^(?:\/(?:(' . $admin . ')(?:\\/([a-zA-Z0-9_\\-\\.\\;\\:]+)(?:\\/([a-zA-Z0-9_\\-\\.\\;\\:]+)(?:[\\/\\?](.*))?)?)?))[\/]*$/',
										   array($admin, 'controller', 'action'), array());
			   }
		  }
	 }
/**
 * Returns this object's routes array. Returns false if there are no routes available.
 *
 * @param string $route	An empty string, or a route string "/"
 * @param array $default NULL or an array describing the default route
 * @return array Array of routes
 */
	 function connect($route, $default = null) {
		  $parsed = $names = array();
		
		  $r = null;
		  if (($route == '') || ($route == '/')) {
			   $regexp='/^[\/]*$/';
			   $this->routes[] = array($route, $regexp, array(), $default);
		  } else {
			   $elements = array();

			   foreach(explode('/', $route)as $element) {
					if (trim($element))
					$elements[] = $element;
			   }

			   if (!count($elements)) {
					return false;
			   }

			   foreach($elements as $element) {
					if (preg_match('/^:(.+)$/', $element, $r)) {
						 $parsed[]='(?:\/([^\/]+))?';
						 $names[] =$r[1];
					} elseif(preg_match('/^\*$/', $element, $r)) {
						 $parsed[] = '(?:\/(.*))?';
					} else {
						 $parsed[] = '/' . $element;
					}
			   }

			   $regexp='#^' . join('', $parsed) . '[\/]*$#';
			   $this->routes[] = array($route, $regexp, $names, $default);
		  }
		  return $this->routes;
	 }
/**
 * Parses given URL and returns an array of controllers, action and parameters
 * taken from that URL.
 *
 * @param string $url URL to be parsed
 * @return array
 * @access public
 */
	 function parse($url) {		  
		  //admin route
		  if ($this->__admin != null && defined('COLA_ADMIN'))
		  {
			   $this->routes[] = $this->__admin;
			   $this->__admin = null;
		  }
		  //default route
		  if ($this->__default_route != null)
		  {
			   $this->routes[] = $this->__default_route;
			   $this->__default_route = null;
		  }
		  
		  if ($url && ('/' != $url[0])) {
			   if (!defined('SERVER_IIS')) {
					$url = '/' . $url;
			   }
		  }
		  $out = array('pass'=>array());
		  $r = null;
		  if (strpos($url, '?') !== false) {
			  $url = substr($url, 0, strpos($url, '?'));
		  }
  
		  foreach($this->routes as $route) {
			   list($route, $regexp, $names, $defaults) = $route;
  
			   if (preg_match($regexp, $url, $r)) {
					// remove the first element, which is the url
					array_shift ($r);
					// hack, pre-fill the default route names
					foreach($names as $name) {
						 $out[$name] = null;
					}
					$ii=0;
  
					if (is_array($defaults)) {
						foreach($defaults as $name => $value) {
							if (preg_match('#[a-zA-Z_\-]#i', $name)) {
								$out[$name] =  $this->stripEscape($value);
							} else {
								$out['pass'][] =  $this->stripEscape($value);
							}
						}
					}
  
					foreach($r as $found) {
						 // if $found is a named url element (i.e. ':action')
						 if (isset($names[$ii])) {
							  $out[$names[$ii]] = $found;
						 } else {
							  // unnamed elements go in as 'pass'
							  $found = explode('/', $found);
							  $pass = array();
							  foreach($found as $key => $value) {
								   if ($value == "0") {
										$pass[$key] =  $this->stripEscape($value);
								   } elseif ($value) {
										$pass[$key] =  $this->stripEscape($value);
								   }
							  }
							  $out['pass'] = am($out['pass'], $pass);
						 }
					  $ii++;
					}
			   break;
			  }
		  }
		  return $out;
	 }
	 function stripEscape($param) {
		  if(is_string($param) || empty($param)) {
			  $return = preg_replace('/^ *-!/', '', $param);
			  return $return;
		  }
		  foreach($param as $key => $value) {
//这里认为不是一个字符串那么就是一个数组,并且这个数组其下不会再有数组,修改为递归的处理方法
//			   if(is_string($value)) {
//					$return[$key] = preg_replace('/^ *-!/', '', $value);
//			   } else {
//			   		foreach ($value as $array => $string) {
//						 $return[$key][$array] = preg_replace('/^ *-!/', '', $string);
//					}
//			   } 
			   $return[$key] = $this->stripEscape($value);	
		  }
		  return $return;
	 }
}
?>