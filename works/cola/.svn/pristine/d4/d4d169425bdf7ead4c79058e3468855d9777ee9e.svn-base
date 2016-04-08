<?php
/* SVN FILE:: $Id: call.php 1685 2010-05-28 09:17:16Z sparkwang $ */
/**
 * file comments here ...
 *
 * @package			
 * @subpackage		
 * @copyright 		Copyright (c) 2010 tencent
 * @author			sparkwang
 * @version			$LastChangedRevision: 1685 $ 
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$LastChangedDate: 2010-05-28 17:17:16 +0800 (五, 2010-05-28) $
 */

if ($argc < 4) {
	echo <<<HELP
Usage:
	php call.php [DEV] [ServiceName] [Method] [Params ...]


HELP;
	exit();
}

array_shift($argv);
$is_dev = false;
if (strtolower($argv[0]) == 'dev') {
	$is_dev = true;
	array_shift($argv);
}

$service_name = $argv[0];
$method = $argv[1];
$params = array_slice($argv, 2);

require dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'basicapp.php';

define('DEBUG', $is_dev);
vendor('csis');

$service = new CsisService($service_name);
$ret = call_user_func_array(array(&$service, $method), $params);

print_r($ret);
?>