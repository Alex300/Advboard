<?php
/* ====================
[BEGIN_COT_EXT]
Code=advboard
Name=Ads board
Description=Ads board module for Cotonti Siena
Version=3.0.0
Date=2015-07-01
Author=Kalnov Alexey    (kalnovalexey@yandex.ru)
Copyright=(с) 2011-2015 Portal30 Studio http://portal30.ru
Auth_guests=R
Lock_guests=12345A
Auth_members=RW
Requires_modules=users
Recommends_modules=files
Requires_plugins=cotontilib
Recommends_plugins=regioncity
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
firstCrumb=02:radio::1:Add link to advboard Main Page to breadcrumbs
count_admin=03:radio::0:
maxlistsperpage=06:select:5,10,15,20,25,30,40,50,60,70,100,200,500:30:Max categories in list
periodOrder=15:select:asc,desc:desc:Period order
notifyAdminNewAdv=16:radio::1:New adv admin notify?
notifyUserNewComment=17:radio::1:New comment user notify?
expNotifyPeriod=18:select:0,1,5,6,7,8,9,10,15,20,25,30:5:Expire notification for days
guestEmailRequire=20:radio::1:Guest e-mail required?
guestUseCaptcha=21:radio::0:Use captcha?
rssToHeader=35:radio::1:Add Adv Board rss in site header?
[END_COT_EXT_CONFIG]

[BEGIN_COT_EXT_CONFIG_STRUCTURE]
order=01:callback:cot_advboard_config_order():sort:
way=04:select:asc,desc:desc:
maxrowsperpage=07:string::30:
truncatetext=11:string::0:
allowSticky=14:radio::1:Allow Sticky Adv?
compareOn=14:radio::1:Allow compare Ads?
title_require=17:radio::1:Title Require?
city_require=21:radio::0:City Require?
phone_require=24:radio::0:Phone Require?
maxPeriod=27:string::30:Max Adv publication period
allowemptytext=31:radio::0:
enable_comments=34:radio::1:Enable Comments
keywords=37:string:::
metatitle=41:string:::
metadesc=44:string:::
[END_COT_EXT_CONFIG_STRUCTURE]
==================== */

/**
 * Ads board module for Cotonti Siena
 *
 * @package Advboard
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) 2011-2015 Portal30 Studio http://portal30.ru
 *
 */
defined('COT_CODE') or die('Wrong URL');

