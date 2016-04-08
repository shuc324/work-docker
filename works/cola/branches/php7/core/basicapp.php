<?php
/* SVN FILE:: $Id: basicapp.php 3329 2010-08-24 08:40:46Z sparkwang $ */
/**
 * file comments here ...
 *
 * @package
 * @subpackage
 * @copyright 		Copyright (c) 2010 tencent
 * @author			sparkwang
 * @version			$LastChangedRevision: 3329 $
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$LastChangedDate: 2010-08-24 16:40:46 +0800 (二, 2010-08-24) $
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
if (DEBUG)
{
	error_reporting(E_ALL ^ E_STRICT);
}
else
{
	error_reporting(0);
}

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
/**
 * These defines should only be edited if you have cola installed in
 * a directory layout other than the way it is distributed.
 * Each define has a commented line of code that explains what you would change.
 *
 */

if (!defined('COLA_CORE_INCLUDE_PATH')) {
	define('COLA_CORE_INCLUDE_PATH', dirname(dirname(__FILE__)));
}
if (!defined('VENDORS')) {
	define ('VENDORS', COLA_CORE_INCLUDE_PATH . DS . 'vendors' . DS);
}
ini_set('include_path', COLA_CORE_INCLUDE_PATH . PATH_SEPARATOR . COLA_CORE_INCLUDE_PATH . DS .'vendors' . PATH_SEPARATOR . ini_get('include_path'));

if (!defined('TMP')) {
	if (defined('APP')) {
		define ('TMP', APP.'tmp'.DS);
	} else {
		define('TMP', '/tmp');
	}
}

require COLA_CORE_INCLUDE_PATH . DS . 'core' . DS . 'libs' . DS . 'mysqldb.php';

if (!function_exists('vendor')) {
	function vendor($name) {
		$args = func_get_args();
		foreach($args as $arg) {
			if (defined('APP') && file_exists(APP . 'vendors' . DS . $arg . '.php')) {
				require_once(APP . 'vendors' . DS . $arg . '.php');
			} else {
				require_once(VENDORS . $arg . '.php');
			}
		}
	}
}

/**
 * Prints out debug information about given variable.
 *
 * Only runs if DEBUG level is non-zero.
 *
 * @param boolean $var		Variable to show debug information for.
 * @param boolean $show_html	If set to true, the method prints the debug data in a screen-friendly way.
 */
if (!function_exists('debug')) {
	function debug($var = false, $showHtml = false) {
		if (DEBUG > 0) {
			print "\n<pre class=\"cola_debug\">\n";
			ob_start();
			print_r($var);
			$var = ob_get_clean();

			if ($showHtml) {
				$var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
			}
			print "{$var}\n</pre>\n";
		}
	}
}
?>
