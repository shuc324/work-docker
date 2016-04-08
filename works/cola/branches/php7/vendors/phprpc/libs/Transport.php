<?php
/* SVN FILE:: $Id: Transport.php 4360 2010-11-01 07:37:24Z sparkwang $ */
/**
 * stream socket tranport class
 *
 * @package			phprpc
 * @subpackage		libs
 * @copyright 		Copyright (c) 2010 tencent
 * @author			sparkwang
 * @version			$LastChangedRevision: 4360 $ 
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$LastChangedDate: 2010-11-01 15:37:24 +0800 (一, 2010-11-01) $
 */

/**
 * stream socket tranport 
 *
 * @package phprpc
 * @subpackage libs
 * @author sparkwang
 */
class Transport {
	protected $handle_; 
	protected $sendTimeout_ = 100;
	protected $recvTimeout_ = 750;
	protected $sendTimeoutSet_ = FALSE;
	
	/**
	 * Transport constructor
	 *
	 * @param resource $socket Stream socket
	 * @author sparkwang
	 */
	function __construct($socket) {
		$this->handle_ = $socket;
	}
	
	/**
	 * Read all of one message
	 *
	 * @return void
	 * @author sparkwang
	 */
	function readAll() {
		$len = $this->read_(4);
		$len = unpack('N', $len);
		$len = $len[1];
		if ($len > 0x7fffffff) {
			$len = 0 - (($len - 1) ^ 0xffffffff);
		}
		if (!$len) return;
		return $this->read_($len);
	}
	
	/**
	 * Write data
	 *
	 * @param string $data 
	 * @return void
	 * @author sparkwang
	 */
	function writeAll($data) {
		$count = strlen($data);
		$this->write_(pack('N', $count) . $data);
	}
	
	function read_($len) {
		if ($this->sendTimeoutSet_) {
			stream_set_timeout($this->handle_, 0, $this->recvTimeout_*1000);
			$this->sendTimeoutSet_ = FALSE;
		}
		$pre = null;
		while (TRUE) {
			$buf = @fread($this->handle_, $len);
			if ($buf === FALSE || $buf === '') {
				$md = stream_get_meta_data($this->handle_);
				if ($md['timed_out']) {
					throw new Exception('Transport: timed out reading '.$len.' bytes from ');
				} else {
					throw new Exception('Transport: Could not read '.$len.' bytes from ');
				}
			} else if (($sz = strlen($buf)) < $len) {
				$md = stream_get_meta_data($this->handle_);
				if ($md['timed_out']) {
					throw new Exception('Transport: timed out reading '.$len.' bytes from ');
				} else {
					$pre .= $buf;
					$len -= $sz;
				}
			} else {
				return $pre.$buf;
			}
		}
	}

	function write_($buf) {
		if (!$this->sendTimeoutSet_) {
			stream_set_timeout($this->handle_, 0, $this->sendTimeout_*1000);
			$this->sendTimeoutSet_ = TRUE;
		}
		while (strlen($buf) > 0) {
			$got = fwrite($this->handle_, $buf);
			if ($got === 0 || $got === FALSE) {
				$md = stream_get_meta_data($this->handle_);
				if ($md['timed_out']) {
					throw new Exception('Transport: timed out writing '.strlen($buf));
				} else {
					throw new Exception('Transport: Could not write buf');
				}
			}
			$buf = substr($buf, $got);
		}
	}

	function flush_() {
		$ret = fflush($this->handle_);
	}
}

?>
