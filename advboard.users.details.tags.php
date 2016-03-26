<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.details.tags
Tags=users.details.tpl:{USERS_DETAILS_ADVERT_SUBMITNEW}, {USERS_DETAILS_ADVERT_SUBMITNEW_URL}, {USERS_DETAILS_ADVERT_COUNT}, {USERS_DETAILS_ADVERT_URL}
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

$submitNewAdv = '';
$submitNewAdvUrl = '';

if (cot_auth('advboard', 'any', 'W') || cot::$usr["isadmin"]){
    $submitNewAdvUrl = cot_url('advboard', array('a'=>'edit'));
    $submitNewAdv = cot_rc_link($submitNewAdvUrl, cot::$L['advboard_add_new']);
}

$advUnValidated = ($urr['user_id'] == cot::$usr['id'] || cot::$usr["isadmin"] || cot_auth('advboard', 'any', 'A'));

$advCond = array(
    array('user',$urr['user_id'] )
);

if (!$advUnValidated){
    $advCond[] = array('begin', cot::$sys['now'], '<=');
    $advCond[] = array('SQL', "expire = 0 OR expire > ".cot::$sys['now']);
    $advCond[] = array('state', advboard_model_Advert::PUBLISHED);
}

//$advUrlParams = array('m'=>'details', 'id'=>$urr['user_id'],'u'=>$urr['user_name']);
//
//$advAjaxPParams = array('a'=>'userDetailsAdvList', 'uid'=>$urr['user_id']);

$t->assign(array(
    "USERS_DETAILS_ADVERT_SUBMITNEW" => $submitNewAdv,
    "USERS_DETAILS_ADVERT_SUBMITNEW_URL" => $submitNewAdvUrl,
    "USERS_DETAILS_ADVERT_COUNT" => advboard_model_Advert::count($advCond),
    "USERS_DETAILS_ADVERT_URL" => cot_url('advboard', array('m' => 'user', 'uid' => $urr['user_id'])),
//    "USERS_DETAILS_ADVS" => ab_advList('advboard.ud_advlist', 10, 'page_begin DESC', $advCond, '', '', '', true, 'ad',
//        false, $advUrlParams, $cfg["turnajax"], $advAjaxPParams),
));