--TEST--
Csis: Use get method to access the csis service
--FILE--
<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test_env.php');
define('CSIS_PROTOCOL', 'socket');

require_once 'csis.php';
$service = new CsisService('TestService');
$ret = $service->add(3, 2);
echo $ret;
?>

--EXPECT--
5