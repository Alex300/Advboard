<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
[END_COT_EXT]
==================== */
/**
 * Ads board module for Cotonti Siena
 *
 * @package Advert
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) 2015 Portal30 Studio http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL');

if(!defined('COT_ADMIN') && cot::$cfg['advert']['rssToHeader'] == 1 && cot_module_active('rss')){

    require_once cot_incfile('advert', 'module');

    // Получить все категории
    if (!empty($structure['advert'])) {
        foreach ($structure['advert'] as $adv_rssCode => $adv_rssCat) {
            if($adv_rssCat['count'] == 0 || !cot_auth('advert', $adv_rssCode, 'R')) continue;

            $advCatTitle = htmlspecialchars($adv_rssCat['title']);

            $adv_rssUrl = cot_url('advert', array('m'=>'rss', 'c'=>$adv_rssCode));
            if (!cot_url_check($adv_rssUrl)) $adv_rssUrl = COT_ABSOLUTE_URL . $adv_rssUrl;

            cot::$out['head_head'] .= "\n".'<link rel="alternate" type="application/rss+xml" title="'.cot::$L['advert_rss_feed']
                .$advCatTitle.'" href="'.$adv_rssUrl.'" />';
        }

    }
}
