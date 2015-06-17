<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.extrafields.first
[END_COT_EXT]
  ==================== */

/**
 * Ads board module for Cotonti Siena
 *
 * @package Advert
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('advert', 'module');
$extra_whitelist[$db_advert] = array(
	'name' => $db_advert,
	'caption' => cot::$L['Module'].' '.cot::$L['advert_ads_board'],
	'type' => 'module',
	'code' => 'advert',
	'tags' => array(

	)
);
