<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
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

if (!defined('COT_ADMIN') && !COT_AJAX){
    require_once cot_incfile('advboard', 'module');

    /**
     * Send Expire notifications
     */
    advboard_controller_User::sendExpireNotify();
}

