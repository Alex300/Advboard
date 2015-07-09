<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.extrafields.first
[END_COT_EXT]
  ==================== */

/**
 * Ads board module for Cotonti Siena
 *
 * @package Advboard
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('advboard', 'module');
$extra_whitelist[$db_advboard] = array(
	'name' => $db_advboard,
	'caption' => cot::$L['Module'].' '.cot::$L['advboard_ads_board'],
	'type' => 'module',
	'code' => 'advboard',
	'tags' => array(

	)
);
