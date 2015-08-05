<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.tags
[END_COT_EXT]
==================== */
/**
 * Ads board module for Cotonti Siena
 *
 * @package Advboard
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright Portal30 Studio http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL.');

if (!COT_AJAX && defined('COT_ADMIN') && cot::$cfg['admintheme'] == 'cpanel'){

    require_once cot_langfile('advboard', 'module');

    $admin_MenuUser['advboard'] = array(
        'title' => cot::$L['advboard_my_ads'],
        'url' => cot_url('advboard', array('m'=>'user')),
        'icon_class' => 'fa fa-file-text',
    );

}
