<?php
require_once dirname(__FILE__) . '/../rpcserver.php';

function test_hello($name) {
	return "hello, $name";
}

class TestService {
	function hello($name) {
		usleep(1000 * 500);
		return "hello, $name";
	}
}

$pid = pcntl_fork();
if ($pid) exit();
$server = new RpcServer('0.0.0.0', 9090, 'TestService');
$server->addService('test_hello');
$server->start();

?>
