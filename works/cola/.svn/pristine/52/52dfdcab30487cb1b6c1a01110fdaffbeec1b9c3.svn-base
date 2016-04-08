<?php
require_once dirname(__FILE__) . '/../rpcclient.php';

$begin = microtime(true);
for ($i = 0; $i < 1; $i++) {
	$pid = 0;//pcntl_fork();
	if ($pid == 0) {
		//$client = new RpcClient('localhost', 9090, 'TestService');
		$client = new RpcClient('tcp://localhost:9090/');
		for ($j = 0; $j < 1; $j++) {
			//$ret = $client->__call(array('TestService', 'hello'), 'world!');
			$ret = $client->test_hello('world!');
		}
		print_r($ret . "\n");
		$due = microtime(true) - $begin;
		print_r("due: $due\n");
		exit();
	}
}
?>