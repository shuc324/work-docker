<?php
/* SVN FILE: $Id: bootstrap.php 7526 2011-07-06 02:03:11Z sparkwang $ */
/**
 * Basic Cola functionality.
 *
 * Core functions for including other source files, loading models and so forth.
 *
 * PHP versions 4 and 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @package			cola
 * @subpackage		cola.core
 * @version			$Revision: 7526 $
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$Date: 2011-07-06 10:03:11 +0800 (三, 2011-07-06) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Configuration, directory layout and standard libraries
 */


if (!isset($bootstrap)) {
	require CORE_PATH . 'core' . DS . 'basics.php';
	require APP_PATH . 'config' . DS . 'core.php';
	require CORE_PATH . 'core' . DS . 'config' . DS . 'paths.php';
}

function set_loadavg_protect($max_proccesses = 20, $tip = null) {
	$file = defined('LOADAVG_FILE') ? LOADAVG_FILE : '/proc/loadavg';
	if($fp = @fopen($file, 'r')) {
		list($loadaverage) = explode(' ', fread($fp, 6));
		fclose($fp);
		if($loadaverage > $max_proccesses) {
			header("HTTP/1.0 503 Service Unavailable");
			$tip = !empty($tip) ? $tip : "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /><title>Service Unavailable</title><script>var reload = function(){window.location.reload()};setTimeout(reload, 5000);</script></head><body><div>System is busy now, please <a href=\"javascript:reload();\">click to retry</a></div></body></html>";
			echo $tip;
			exit();
		}
	}
}

if (!defined('MAX_LOADAVG')) {
	set_loadavg_protect(20);
} else {
	set_loadavg_protect(MAX_LOADAVG);
}

$TIME_START = microtime(true);

cola_auto_session();

require LIBS . 'object.php';
require LIBS . 'inflector.php';
require LIBS . 'configure.php';
$paths = Configure::getInstance();

/**
 * Enter description here...
 */
if (empty($uri) && defined('BASE_URL')) {
	$uri = setUri();

	if ($uri === '/' || $uri === '/index.php' || $uri === '/'.APP_DIR.'/') {
		$_GET['url'] = '/';
		$url = '/';
	} else {
		if (strpos($uri, 'index.php') !== false) {
			$uri = r('?', '', $uri);
			$elements=explode('/index.php', $uri);
		} else {
			$elements = explode('/?', $uri);
		}

		if (!empty($elements[1])) {
			$_GET['url'] = $elements[1];
			$url = $elements[1];
		} else {
			$_GET['url'] = '/';
			$url = '/';
		}
	}
} else {
	if (empty($_GET['url'])) {
		$url = null;
	} else {
		$url = $_GET['url'];
	}
}

if (strpos($url, 'css/') === 0) {
	include WWW_ROOT . DS . 'css.php';
	die();
}

Configure::write('debug', DEBUG);

require COLA . 'dispatcher.php';

if (defined('OPEN_API')) {
	
}

if (defined('CACHE_CHECK') && CACHE_CHECK === true) {
	if (empty($uri)) {
		$uri = setUri();
	}
	$filename=CACHE . 'views' . DS . convertSlash($uri) . '.php';

	if (file_exists($filename)) {
		uses(DS . 'view' . DS . 'view');
		$v = null;
		$view = new View($v);
		$view->renderCache($filename, $TIME_START);
	} elseif(file_exists(CACHE . 'views' . DS . convertSlash($uri) . '_index.php')) {
		uses(DS . 'view' . DS . 'view');
		$v = null;
		$view = new View($v);
		$view->renderCache(CACHE . 'views' . DS . convertSlash($uri) . '_index.php', $TIME_START);
	}
}
?>
