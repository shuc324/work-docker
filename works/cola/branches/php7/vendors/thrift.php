<?php
$GLOBALS['THRIFT_ROOT'] = realpath(dirname(__FILE__)).'/thrift';
require_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';
require_once $GLOBALS['THRIFT_ROOT'].'/protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TServerTransport.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TNonblockingServerSocket.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TBufferedTransport.php';
require_once $GLOBALS['THRIFT_ROOT'].'/transport/TNonblockingSocket.php';
require_once $GLOBALS['THRIFT_ROOT'].'/server/TServer.php';
require_once $GLOBALS['THRIFT_ROOT'].'/server/TNonblockingServer.php';
?>