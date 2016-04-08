<?php
/* SVN FILE: $Id: validators.php 1462 2010-05-12 09:31:01Z sparkwang $ */
/**
 * Tort Validators
 *
 * Used to validate data in Models.
 *
 * PHP versions 4 and 5
 *
 * @package			cola
 * @subpackage		cola.core.libs
 * @since			ColaPHP(tm) v 0.2.9
 * @version			$Revision: 1462 $
 * @modifiedby		$LastChangedBy: sparkwang $
 * @lastmodified	$Date: 2010-05-12 17:31:01 +0800 (三, 2010-05-12) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Not empty.
 */
	define('VALID_NOT_EMPTY', '/.+/');
/**
 * Numbers [0-9] only.
 */
	define('VALID_NUMBER', '/^[-+]?\\b[0-9]*\\.?[0-9]+\\b$/');
/**
 * A valid email address.
 */
	define('VALID_EMAIL', '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i');
/**
 * A valid year (1000-2999).
 */
	define('VALID_YEAR', '/^[12][0-9]{3}$/');
?>