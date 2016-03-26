<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.edit.update.delete
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

// Удалить все объявления пользователя
$condition = array(array('user', $id));
$advertisement = advboard_model_Advert::find($condition);
if(!empty($advertisement)) {
    foreach($advertisement as $advert) {
        $advert->delete();
        unset($advert);
    }
    unset($advertisement);
}