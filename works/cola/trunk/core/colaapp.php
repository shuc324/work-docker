<?php
/**
 * 适用于基于COLA的应用后台脚本包含文件
 * 加载本文件后，可使用基于COLA的应用的数据库配置及环境
 *
 * @author 				lightma			
 * @package				
 * @subpackage			
 * @version				
 * @modifieldby			lightma
 * @lastmodified		Date: 2010-07-13 16:35:37
 * @license				
 */
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('COLA_CORE_INCLUDE_PATH')) {
	define('COLA_CORE_INCLUDE_PATH', dirname(dirname(__FILE__)));
}

if (defined('ROOT')) {
	if (!defined('APP_DIR')) {
		define('APP_DIR', 'app');
	}

	if (!defined('WEBROOT_DIR')) {
		define('WEBROOT_DIR', 'webroot');
	}
	if (!defined('WWW_ROOT')) {
		define('WWW_ROOT', ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS);
	}
	if (!defined('CORE_PATH')) {
		if (function_exists('ini_set')) {
			ini_set('include_path', ROOT . DS . APP_DIR . DS
			. PATH_SEPARATOR . ROOT . DS . APP_DIR. DS. 'vendors'
			. PATH_SEPARATOR. ini_get('include_path'));

			define('APP_PATH', null);
			define('CORE_PATH', null);
		} else {
			define('APP_PATH', ROOT . DS . APP_DIR . DS);
			define('CORE_PATH', COLA_CORE_INCLUDE_PATH . DS);
		}
	}

	if (!defined('COLA')) {
		define ('COLA', CORE_PATH.'core'.DS);
	}
	if (!defined('APP')) {
		define ('APP', ROOT.DS.APP_DIR.DS);
	}

	require_once APP . 'config' . DS . 'core.php';
	require_once APP . 'config' . DS . 'database.php';

	function loadCola() {
		require CORE_PATH . 'core' . DS . 'basics.php';
		require CORE_PATH . 'core' . DS . 'config' . DS . 'paths.php';

		require LIBS . 'object.php';
		require LIBS . 'inflector.php';
		require LIBS . 'configure.php';
		$paths = Configure::getInstance();
	}
}
require_once(COLA_CORE_INCLUDE_PATH . DS . 'core'. DS . 'basicapp.php' );
