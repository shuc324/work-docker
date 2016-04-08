<?php
/* SVN FILE:: $Id: EventBase.php 4360 2010-11-01 07:37:24Z sparkwang $ */
/**
 * Rpc server golbal event base class
 *
 * @package			phprpc
 * @subpackage		server
 * @copyright 		Copyright (c) 2010 tencent
 * @author			sparkwang
 * @version			$LastChangedRevision: 4360 $ 
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$LastChangedDate: 2010-11-01 15:37:24 +0800 (一, 2010-11-01) $
 */

/**
 * Global event base
 *
 * @package phprpc
 * @subpackage server
 * @author sparkwang
 */
class EventBase {
	protected static $base_;
	
	/**
	 * Get the global event base
	 *
	 * @return void
	 * @author sparkwang
	 */
	public static function getBase() {
		if (self::$base_ == NULL) {
			self::$base_ = event_base_new();
		}
		return self::$base_;
	}
	
	/**
	 * Event loop on the global base
	 *
	 * @return void
	 * @author sparkwang
	 */
	public static function loop() {
		event_base_loop(self::$base_);
	}
}

?>
