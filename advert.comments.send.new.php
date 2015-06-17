<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=comments.send.new
[END_COT_EXT]
==================== */
/**
 * Ads board module for Cotonti Siena
 *
 * @package Advert
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) 2015 Portal30 Studio http://portal30.ru
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('advert', 'module');

$adv_cat = '';
if ($comarray['com_area'] == 'advert' && cot::$cfg['advert']['notifyUserNewComment'] == 1 && $comarray['com_code'] > 0){
    $advert = advert_model_Advert::getById($comarray['com_code']);
    if($advert && $advert->issetEmail(true)) {
        // Очистить кеш
        // todo очистка кеша в одном файле с мультихуком
//        if (ab_inBoardCat($adv_cat) && $cache && $cfg['plugin']['advboard']['recentAdvCacheTime'] > 0) {
//            /** @var Memcache_driver $ab_CacheDrv  */
//            $ab_CacheDrv = ab_getCacheDrv();
//            $cache_key = 'RECENT_ADVS';
//            $advRealm = COT_DEFAULT_REALM;
//            $ab_CacheDrv->remove($cache_key, $advRealm);
//        }
        //Очистить кеш

        $advertUrl = $advert->getUrl();
        if (!cot_url_check($advertUrl)) $advertUrl = COT_ABSOLUTE_URL . $advertUrl;

        $advertEditUrl = $advert->getEditUrl();
        if (!cot_url_check($advertEditUrl)) $advertEditUrl = COT_ABSOLUTE_URL . $advertEditUrl;

        $myAdvsUrl = cot_url('advert', 'm=user');
        if (!cot_url_check($myAdvsUrl)) $myAdvsUrl = COT_ABSOLUTE_URL . $myAdvsUrl;

        $tmpL = cot::$L;

        $text = $advert->description;
        if(empty($text)) $text = $advert->text;

        $user = null;
        $userLang = cot::$cfg['defaultlang'];

        $owner = $advert->getOwner();
        if($owner['user_id'] > 0) {
            if(cot::$cfg['defaultlang'] != $owner['user_lang']) {
                $userLang = $owner['user_lang'];
                include cot_langfile('main', 'core', cot::$cfg['defaultlang'], $owner['user_lang']);
                include cot_langfile('advert', 'module', cot::$cfg['defaultlang'], $owner['user_lang']);
            }
        }

        // Выдержка с поста
//        $len_cut = 500;  // Длина выдержки с поста (символов)
        $advComText = cot_parse($comarray['com_text'], cot::$cfg['plugin']['comments']['markup']);
//        $advComText = cot_string_truncate($advComText, $len_cut, true, false, '...');
        // /Выдержка с поста

        // Автор комментария
        $advCommenterName = cot::$L['Anonymous'];
        $advCommenterUrl = '';
        if(cot::$usr['id'] > 0) {
            $advCommenterName = cot_user_full_name(cot::$usr['profile']);
            $advCommenterUrl  = cot_url('users', array('m'=>'details', 'id' => cot::$usr['id'], 'u' => htmlspecialchars(cot::$usr['name']) ) );
            if (!cot_url_check($advCommenterUrl)) $advCommenterUrl = COT_ABSOLUTE_URL . $advCommenterUrl;
        } elseif($comarray['com_author'] != '') {
            $advCommenterName = $comarray['com_author'];
        }
        // /Автор комментария

        $mailView = new View();
        $mailView->advert = $advert;
        $mailView->owner = $owner;
        $mailView->commentText = $advComText;
        $mailView->commentUrl = $advertUrl."#c".$id;
        $mailView->commenter = cot::$usr['id'] > 0 ? cot::$usr['profile'] : array();
        $mailView->commenterName = $advCommenterName;
        $mailView->commenterUrl = $advCommenterUrl;
        $mailView->advertUrl = $advertUrl;
        $mailView->advertEditUrl = $advertEditUrl;
        $mailView->myAdvsUrl = $myAdvsUrl;
        $mailView->advertText = $text;

        $mailSubject = cot::$L['advert_new_comment'];
        $mailBody = $mailView->render('advert.notify_comment.' . $userLang . '.' . $advert->category);

        cot_mail($advert->getEmail(false, true), $mailSubject, $mailBody, '', false, null, true);

        // Вернем язык на место
        cot::$L = $tmpL;

    }
}