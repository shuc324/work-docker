<?php
/* SVN FILE:: $Id: apis_controller.php 7544 2011-07-06 08:24:59Z sparkwang $ */

/**
 * Apis controller class.
 *
 * PHP versions 4 and 5
 *
 * @package			cola
 * @subpackage		cola.core.libs.controller
 * @since			ColaPHP(tm) v 0.2.9
 * @version			$Revision: 7544 $
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$Date: 2011-07-06 16:24:59 +0800 (三, 2011-07-06) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
 
/**
 * ApisController
 *
 * Application apis controller
 *
 * @package		cola
 * @subpackage	cola.core.libs.controller
 *
 */
if (defined('AUTOAPI') && AUTOAPI) {
	uses('controller/apis_controller_base');
	class ApisController extends ApisControllerBase {
	}
}