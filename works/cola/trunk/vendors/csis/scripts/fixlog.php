<?php
/* SVN FILE:: $Id: fixlog.php 1763 2010-06-02 09:09:39Z sparkwang $ */
/**
 * fix the cisi failed call
 *
 * @package
 * @subpackage
 * @copyright 		Copyright (c) 2010 tencent
 * @author			sparkwang
 * @version			$LastChangedRevision: 1763 $
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$LastChangedDate: 2010-06-02 17:09:39 +0800 (三, 2010-06-02) $
 */

if (!isset($argc) || !$argc || !isset($argv)) {
	$argv = array(__FILE__);
}
if (count($argv) < 3) {
	echo <<<HELP
Usage: php shell.php [show|count|redo] [csis_failed.log]\r\n
HELP;
	exit();
}


define('DEBUG', '1');
define('CSIS_PROTOCOL', 'soap');

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'basicapp.php';
vendor('csis');


$action = $argv[1];
$log_file = $argv[2];

$line_count = 0;
$redo_count = 0;
$redo_error_count = 0;
$redo_success = 0;
$_fixlog = '_fixlog' . time() . '.log';

$handle = @fopen($log_file, "r");
if ($handle) {
	while (!feof($handle)) {
		$buffer = fgets($handle, 32768);
		preg_match('/\[error\]\s(.*)/', $buffer, $matches);
		$data = @$matches[1];
		if (!empty($data)) {
			switch($action) {
				case 'show':
					print_r($data . "\r\n");
					break;
				case 'redo':
					$data = json_decode($data, true);
					unset($data['__error']);
					redo($data);
					break;
			}
			$line_count++;
		}
	}
	fclose($handle);
}

if ($action == 'count' || $action == 'show') {
	echo "Total: " . $line_count . " \r\n";
}

function redo($data) {
	$service = $data['service'];
	$method = $data['method'];
	$params = $data['params'];
	$cs = new CsisService($service);
	global $_fixlog ;
	$cs->logfailed($_fixlog);
	
	print_r($data);
	global $redo_error_count;
	global $redo_success;
	$ret = null;
	try {
		$ret = $cs->invoke($method, $params);
		$redo_success++;
	}catch(Exception $e) {
		$redo_error_count++;
	}
	
	echo $redo_success . "\r\n";
	echo $redo_error_count . "\r\n";
}

if ($action == 'redo') {
	if ($redo_error_count > 0) {
		echo "Warning: $redo_error_count fix failed, $redo_success success, , see the _fixlogxxxxx.log\r\n";
	} else {
		echo "$redo_success success\r\n";
	}
}

?>