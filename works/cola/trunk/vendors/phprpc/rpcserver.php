<?php
/* SVN FILE:: $Id: rpcserver.php 4360 2010-11-01 07:37:24Z sparkwang $ */
/**
 * 
 *
 * @package			phprpc
 * @subpackage		server
 * @copyright 		Copyright (c) 2010 tencent
 * @author			sparkwang
 * @version			$LastChangedRevision: 4360 $ 
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$LastChangedDate: 2010-11-01 15:37:24 +0800 (一, 2010-11-01) $
 */
require_once dirname(__FILE__) . '/libs/Thread.php';
require_once dirname(__FILE__) . '/libs/Transport.php';
require_once dirname(__FILE__) . '/libs/JsonRpc.php';
require_once dirname(__FILE__) . '/server/EventBase.php';
require_once dirname(__FILE__) . '/server/ServerSocket.php';
require_once dirname(__FILE__) . '/server/RpcProcessor.php';
require_once dirname(__FILE__) . '/server/RpcServer.php';
?>
