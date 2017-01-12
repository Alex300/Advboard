<?php
defined('COT_CODE') or die('Wrong URL.');


/**
 * Ads board module for Cotonti Siena
 *     advboard_controller_User
 *     User Controller class
 *
 * @package Advboard
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */
class advboard_controller_User
{
    public function indexAction() {
        global $structure;

        $uid = cot_import('uid', 'G', 'INT');
        if(empty($uid)) $uid = cot::$usr['id'];

        // Незарегов, если они не смотрят объявления другого пользователя перенаправляем
        if (!$uid) cot_redirect(cot_url('advboard', '', '', true));

        // Проверить существование пользователя
        $user = cot_user_data($uid);
        if(!$user) cot_die_message(404, TRUE);

        $maxrowsperpage = cot::$cfg['advboard']['cat___default']['maxrowsperpage'];
        if($maxrowsperpage < 1) $maxrowsperpage = 1;

        list($pg, $d, $durl) = cot_import_pagenav('d', $maxrowsperpage); //page number for pages list

        $condition = array(
            array('user', $uid),
        );

        if(!cot::$usr['isadmin'] && $uid != cot::$usr['id']) {
            $condition[] = array('state', advboard_model_Advert::PUBLISHED);
            $condition[] = array('begin', cot::$sys['now'], '<=');
            $condition[] = array('SQL', "expire = 0 OR expire > ".cot::$sys['now']);
        }

        $order = array(
            array('state', 'ASC'),
            array('sort', 'DESC'),
        );

        cot::$out['canonical_uri'] = cot_url('advboard', array('m'=>'user', 'uid'=>$uid));

        $urlParams = array('m'=>'user');
        if($uid != cot::$usr['id']) $urlParams['uid'] = $uid;

        $title = '';

        $crumbs = array();
        if($uid != cot::$usr['id']) {
            cot::$out['subtitle'] = $title = cot::$L['advboard_user_ads'].': '.cot_user_full_name($user);
            $crumbs[] = array(cot_url("users"), cot::$L['Users']);
            $crumbs[] = array(cot_url("users", "m=details&id=".$user["user_id"]."&u=".$user["user_name"] ),
                cot_user_full_name($user));
            $crumbs[] = cot::$L['advboard_user_ads'];
//            $advUrlParams['uid']  = $user['user_id'];
            $urlParams['uid'] = $user['user_id'];
        } else {
            cot::$out['subtitle'] = $title = cot::$L['advboard_my_ads'];
            $crumbs[] = array(cot_url('users', array('m'=>'details')), cot::$L['advboard_my_page']);
            $crumbs[] = cot::$L['advboard_my_ads'];
        }

        /* === Hook === */
        foreach (cot_getextplugins('advboard.user.list.query') as $pl) {
            include $pl;
        }
        /* ===== */

        $totallines = advboard_model_Advert::count($condition);
        $advertisement = advboard_model_Advert::findByCondition($condition, $maxrowsperpage, $d, $order);

        $addNewUrl = '';
        if((cot::$usr['auth_write'] || cot::$usr['isadmin']) && !empty($structure['advboard'])){
            // Ищем категорию куда пользователь может подать оьбъявление
            foreach($structure['advboard'] as $catCode => $catRow) {
                $auth_write = cot_auth('advboard', $catCode, 'W');
                if($auth_write) {
                    $addNewUrl = cot_url('advboard', array('a'=>'edit', 'c'=>$catCode));
                    break;
                }
            }
        }

        $pagenav = cot_pagenav('advboard', $urlParams, $d, $totallines, $maxrowsperpage);
        if(empty($pagenav['current'])) $pagenav['current'] = 1;

        $breadcrumbs = '';
        if(!empty($crumbs)) $breadcrumbs = cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb'], true);

        $template = array('advboard', 'list', 'user');

        $pageUrlParams = $urlParams;
        if($durl > 1) $pageUrlParams['d'] = $durl;

        $view = new View();
        $view->breadcrumbs = $breadcrumbs;
        $view->page_title = htmlspecialchars($title);
        $view->advertisement = $advertisement;
        $view->allowComments = true;
        $view->totalitems = $totallines;
        $view->pagenav = $pagenav;
        $view->addNewUrl = $addNewUrl;
        $view->urlParams = $urlParams;
        $view->pageUrlParams = $pageUrlParams;

        /* === Hook === */
        foreach (cot_getextplugins('advboard.user.list.view') as $pl) {
            include $pl;
        }
        /* ===== */

        return $view->render($template);
    }


    /**
     * Рассылка уведомлений об истечении сроков публикации объявления
     */
    public static function sendExpireNotify(){
        global $L;

        $cacheFileName = cot::$cfg["modules_dir"].'/advboard/inc/send.txt';

        if (file_exists($cacheFileName)){
            $adv_send = file_get_contents($cacheFileName);
        }else{
            $adv_send = 0;
        }

        $tmp = getdate(cot::$sys['now']);
        $today = mktime(0, 0, 0, $tmp["mon"], $tmp["mday"], $tmp["year"]);

        // Рассылаем раз в сутки
        if ($today - $adv_send >= 86400){
            // Период за который рассылаем
            if ($adv_send == 0){
                // не разу не рассылали еще
                $adv_sendPer = cot::$cfg['advboard']['expNotifyPeriod'];
            }else{
                $adv_sendPer = floor( ($today - $adv_send) / 86400 );
            }

            // Уведомляем об истечении
            // Пока тупо шлем напоминание всем объявлениям у которых дата истечения
            // Больше той, когда заходили последний раз, но меньше текущей минус <уведомить за>
            $stDay = $tmp["mday"] + cot::$cfg['advboard']['expNotifyPeriod'] - $adv_sendPer;
            $periodStart = mktime(0, 0, 0, $tmp["mon"], $stDay , $tmp["year"]);
            if ($periodStart < cot::$sys['now']) $periodStart  = cot::$sys['now'];

            $periodEnd = mktime(0, 0, 0, $tmp["mon"], $tmp["mday"] + cot::$cfg['advboard']['expNotifyPeriod'], $tmp["year"]);

            $condition = array(
                array('expire', $periodStart, '>='),
                array('expire', $periodEnd, '<'),
                array('state', advboard_model_Advert::PUBLISHED),
                array('user', 0, '>'),
            );

            $advertisement = advboard_model_Advert::findByCondition($condition, 0, 0, array(array('id', 'ASC')));
            $cnt = 0;
            if($advertisement) {
                foreach($advertisement as $advRow) {
                    if(!$advRow->issetEmail(true)) return false;

                    $advertUrl = $advRow->getUrl();
                    if (!cot_url_check($advertUrl)) $advertUrl = COT_ABSOLUTE_URL . $advertUrl;

                    $advertEditUrl = $advRow->getEditUrl();
                    if (!cot_url_check($advertEditUrl)) $advertEditUrl = COT_ABSOLUTE_URL . $advertEditUrl;

                    $myAdvsUrl = cot_url('advboard', 'm=user');
                    if (!cot_url_check($myAdvsUrl)) $myAdvsUrl = COT_ABSOLUTE_URL . $myAdvsUrl;

                    $tmpL = $L;

                    $text = $advRow->description;
                    if(empty($text)) $text = $advRow->text;

                    $user = null;
                    $userLang = cot::$cfg['defaultlang'];

                    $owner = $advRow->getOwner();
                    if($owner['user_id'] > 0) {
                        if(cot::$cfg['defaultlang'] != $owner['user_lang']) {
                            $userLang = $owner['user_lang'];
                            include cot_langfile('main', 'core', cot::$cfg['defaultlang'], $owner['user_lang']);
                            include cot_langfile('advboard', 'module', cot::$cfg['defaultlang'], $owner['user_lang']);
                        }
                    }

                    $mailView = new View();
                    $mailView->advert = $advRow;
                    $mailView->user = $owner;
                    $mailView->advertUrl = $advertUrl;
                    $mailView->advertEditUrl = $advertEditUrl;
                    $mailView->myAdvsUrl = $myAdvsUrl;
                    $mailView->advertText = $text;

                    $mailSubject = cot::$L['advboard_expire_title'];
                    $mailBody = $mailView->render('advboard.notify_expire.' . $userLang . '.' . $advRow->category);
                    if(cot_mail($advRow->getEmail(false, true), $mailSubject, $mailBody, '', false, null, true)) {
                        $cnt++;
                    }
                    // Вернем язык на место
                    $L = $tmpL;


                }
            }

            file_put_contents($cacheFileName, $today);

            return $cnt;
        }

        return 0;
    }
}

