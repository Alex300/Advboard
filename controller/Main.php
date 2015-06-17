<?php
defined('COT_CODE') or die('Wrong URL.');


/**
 * Ads board module for Cotonti Siena
 *     advert_controller_Main
 *     Main Controller class
 *
 * @package Advert
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */
class advert_controller_Main
{
    public function indexAction() {

        $id = cot_import('id', 'G', 'INT');
        $al = cot_import('al', 'G', 'TXT');
        $c = cot_import('c', 'G', 'TXT');

        if (isset($_GET['id']) || (isset($_GET['al']) && !in_array($_GET['al'], array('all', 'unvalidated', 'saved-drafts')) )) {
            return $this->adView();

        } elseif($c != '') {
            return $this->adList($c);
        }

        // Main Page

    }

    /**
     * Список объявлений
     * @param $c
     * @return string
     * @throws Exception
     */
    public function adList($c) {
        global $structure, $cot_extrafields, $db_structure, $Ls;

        if ($c == 'all') {
            cot_block(cot::$usr['isadmin']);

        } elseif (!in_array($c, array('unvalidated', 'saved-drafts'))) {
            if (!isset($structure['advert'][$c])) {
                cot_die_message(404, TRUE);

            } else {
                list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin']) = cot_auth('advert', $c);
                cot_block(cot::$usr['auth_read']);
            }
        }

        $sort = cot_import('s', 'G', 'ALP');       // order field name
        $way = cot_import('w', 'G', 'ALP', 4);    // order way (asc, desc)
        $maxrowsperpage = (cot::$cfg['advert']['cat_' . $c]['maxrowsperpage']) ? cot::$cfg['advert']['cat_' . $c]['maxrowsperpage'] :
            cot::$cfg['advert']['cat___default']['maxrowsperpage'];
        if($maxrowsperpage < 1) $maxrowsperpage = 1;

        list($pg, $d, $durl) = cot_import_pagenav('d', $maxrowsperpage); //page number for pages list
        list($pgc, $dc, $dcurl) = cot_import_pagenav('dc', cot::$cfg['advert']['maxlistsperpage']);// page number for cats list


        // Фильтры для модератора
        $mf = array('period' => 0, 'state' => -2);
        if(cot::$usr['isadmin']) {
            $mf['period'] = isset($_GET['mf']['period']) ? cot_import($_GET['mf']['period'], 'D', 'ALP') : 0;
            $mf['state'] = isset($_GET['mf']['state']) ? cot_import($_GET['mf']['state'], 'D', 'INT') : -2;
        }

        /* === Hook === */
        foreach (cot_getextplugins('advert.list.first') as $pl) {
            include $pl;
        }
        /* ===== */

        $category = array('config' => array());
        if(isset($structure['advert'][$c])) {
            $category = $structure['advert'][$c];
            $category['config'] = cot::$cfg['advert']['cat_' . $c];
        }
        $category['code'] = $c;

        $fields = advert_model_Advert::getColumns();

        if (empty($sort)) {
            $sort = cot::$cfg['advert']['cat_' . $c]['order'];

        } elseif (!in_array($sort, $fields)) {
            $sort = 'sort';
        }
        $way = empty($way) ? cot::$cfg['advert']['cat_' . $c]['way'] : $way;

        $sort = empty($sort) ? cot::$cfg['advert']['cat___default']['order'] : $sort;
        $way = (empty($way) || !in_array($way, array('asc', 'desc'))) ? cot::$cfg['advert']['cat___default']['way'] : $way;

        $urlParams = array('c' => $c);
        if ($sort != cot::$cfg['advert']['cat_' . $c]['order']) $urlParams['s'] = $sort;
        if ($way  != cot::$cfg['advert']['cat_' . $c]['way'])   $urlParams['w'] = $way;

        $canonicalUrlParams = array('c' => $c);
        if ($durl > 1)  $canonicalUrlParams['d'] = $durl;
        if ($dcurl > 1) $canonicalUrlParams['dc'] = $dcurl;


        $template = array('advert', 'list');
        $where = array();

        if ($c == 'unvalidated') {
            $template = array('advert', 'list', 'unvalidated');
            $where['state'] = array('state', advert_model_Advert::AWAITING_MODERATION);
            if(!cot::$usr['isadmin']) $where['user'] = array('user', cot::$usr['id']);
            $category['title'] = cot::$L['page_validation'];
            $category['desc']  = cot::$L['page_validation_desc'];
            $sort = 'created';
            $way = 'desc';

        } elseif ($c == 'saved-drafts') {
            $template = array('advert', 'list', 'unvalidated');
            $where['state'] = array('state', advert_model_Advert::DRAFT);
            if(!cot::$usr['isadmin']) $where['user'] = array('user', cot::$usr['id']);
            $category['title'] = cot::$L['page_drafts'];
            $category['desc']  = cot::$L['page_drafts_desc'];
            $sort = 'created';
            $way = 'desc';

        } elseif ($c == 'all') {
            $category['title'] = cot::$L['advert_ads_board'];

        } else {
            $where['category'] = array('category', $c);
            $where['state'] = array('state', advert_model_Advert::PUBLISHED);
            $where['begin'] = array('begin', cot::$sys['now'], '<=');
            $where['expire'] = array('SQL', "expire = 0 OR expire > ".cot::$sys['now']);
            $template = array('advert', 'list', $structure['advert'][$c]['tpl']);
        }

        $moderatorFilters = array();
        if(cot::$usr['isadmin']) {
            if($mf['state'] == -1) {
                unset($where['state']);
            } elseif($mf['state'] >= 0 && $mf['state'] < 3) {
                $where['state'] = array('state', $mf['state']);
            }

            if($mf['period'] == 'all') {
                unset($where['begin'], $where['expire']);

            } elseif($mf['period'] == 'exp') {
                unset($where['begin']);
                $where['expire'] = array('SQL', "expire > 0 AND expire <= ".cot::$sys['now']);

            } elseif($mf['period'] == 'fut') {
                unset($where['expire']);
                $where['begin'] = array('begin', cot::$sys['now'], '>');
            }
            $tmp = array(
                -2 => cot::$R['code_option_empty'],
                -1 => cot::$L['All'],
                 0 => cot::$L['advert_state_0'],
                 1 => cot::$L['advert_state_1'],
                 2 => cot::$L['advert_state_2'],
            );
            $moderatorFilters['state'] = cot_selectbox($mf['state'], 'mf[state]', array_keys($tmp), array_values($tmp), false);

            $tmp = array(
                '0' => cot::$R['code_option_empty'],
                'all' => cot::$L['All'],
                'exp' => cot::$L['advert_expired'],
                'fut' => cot::$L['advert_future']
            );
            $moderatorFilters['period'] = cot_selectbox(strval($mf['period']), 'mf[period]', array_keys($tmp), array_values($tmp), false);

            $moderatorFilters['action'] = cot_url('advert', $urlParams);
            $moderatorFilters['hidden'] = '';
            foreach($urlParams as $key => $val) {
                $moderatorFilters['hidden'] .= cot_inputbox('hidden', $key, $val);
            }
            $moderatorFilters['reset'] = cot_url('advert', $urlParams);
        }

        cot_die((empty($category['title'])) && !cot::$usr['isadmin']);

        cot::$out['desc'] = htmlspecialchars(strip_tags($category['desc']));
        cot::$out['subtitle'] = $category['title'];
        if (!empty(cot::$cfg['advert']['cat_' . $c]['keywords'])) cot::$out['keywords'] = cot::$cfg['advert']['cat_' . $c]['keywords'];
        if (!empty(cot::$cfg['advert']['cat_' . $c]['metadesc'])) cot::$out['desc'] = cot::$cfg['advert']['cat_' . $c]['metadesc'];
        if (!empty(cot::$cfg['advert']['cat_' . $c]['metatitle'])) cot::$out['subtitle'] = cot::$cfg['advert']['cat_' . $c]['metatitle'];
        // Building the canonical URL
        cot::$out['canonical_uri'] = cot_url('advert', $canonicalUrlParams);

        $condition = array();

        foreach($where as $key => $val) {
            $condition[] = $val;
        }

        $order = array(array('sticky', 'desc'), array($sort, $way));

        /* === Hook === */
        foreach (cot_getextplugins('advert.list.query') as $pl) {
            include $pl;
        }
        /* ===== */

        $totallines = advert_model_Advert::count($condition);
        $advertisement = null;
        if($totallines > 0) $advertisement = advert_model_Advert::find($condition, $maxrowsperpage, $d, $order);

        $allowComments = cot_plugin_active('comments');
        if($allowComments) {
            if(!isset(cot::$cfg['advert']['cat_'.$c])) {
                $allowComments = false;
            } else {
                $allowComments = cot::$cfg['advert']['cat_' . $c]['enable_comments'];
            }
        }

        $addNewUrl = '';
        if((cot::$usr['auth_write'] || cot::$usr['isadmin']) && !empty($category['id'])){
            $addNewUrl = cot_url('advert', array('a'=>'edit', 'c'=>$category['code']));
        }

        /* === Hook === */
        foreach (cot_getextplugins('advert.list.main') as $pl) {
            include $pl;
        }
        /* ===== */

        // Extra fields for structure
        foreach ($cot_extrafields[$db_structure] as $exfld) {
            $uname = $exfld['field_name'];
            $val = $structure['advert'][$c][$exfld['field_name']];
            $category[$uname.'_title'] = isset(cot::$L['structure_'.$exfld['field_name'].'_title']) ?
                cot::$L['structure_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
            $category[$uname] = cot_build_extrafields_data('structure', $exfld, $val);
            $category[$uname.'_value'] = $val;
        }

        $kk = 0;
        $allsub = cot_structure_children('advert', $c, false, false, true, false);
        $subcat = array_slice($allsub, $dc, cot::$cfg['advert']['maxlistsperpage']);

        /* === Hook === */
        foreach (cot_getextplugins('advert.list.rowcat.first') as $pl) {
            include $pl;
        }
        /* ===== */

        /* === Hook - Part1 : Set === */
        $extp = cot_getextplugins('advert.list.rowcat.loop');
        /* ===== */
        $subCategories = array();
        foreach ($subcat as $x) {
            $kk++;
            $cat_childs = cot_structure_children('advert', $x);
            $sub_count = 0;
            foreach ($cat_childs as $cat_child) {
                $sub_count += (int)$structure['advert'][$cat_child]['count'];
            }

            $sub_url_path = $urlParams;
            $sub_url_path['c'] = $x;
            $subCategories[$x] =  $structure['advert'][$x];
            $subCategories[$x]['config'] = cot::$cfg['advert']['cat_' . $x];
            $subCategories[$x]['code'] = $x;
            $subCategories[$x]['count'] = $sub_count;
            $subCategories[$x]['num'] = $kk;

            // Extra fields for structure
            foreach ($cot_extrafields[$db_structure] as $exfld) {
                $uname = $exfld['field_name'];
                $val = $structure['advert'][$x][$exfld['field_name']];
                $subCategories[$x][$uname.'_title'] = isset(cot::$L['structure_'.$exfld['field_name'].'_title']) ?
                    cot::$L['structure_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
                $subCategories[$x][$uname] = cot_build_extrafields_data('structure', $exfld, $val);
                $subCategories[$x][$uname.'_value'] = $val;
            }

            /* === Hook - Part2 : Include === */
            foreach ($extp as $pl) {
                include $pl;
            }
            /* ===== */

        }

        $crumbs = array();
        if(!empty($category['id'])) {
            $crumbs = cot_structure_buildpath('advert', $c);
            if (cot::$cfg['advert']['firstCrumb']) array_unshift($crumbs, array(cot_url('advert'), cot::$L['advert_ads']));
        }

        // Фильтры для модератора
        if(cot::$usr['isadmin']) {
            if($mf['period'] != '0')  $urlParams['mf[period]'] = $mf['period'];
            if($mf['state'] != -2)    $urlParams['mf[state]'] = $mf['state'];
        }

        $pagenavCategory = cot_pagenav('advert', $urlParams + array('d' => $durl), $dc, count($allsub),
            cot::$cfg['advert']['maxlistsperpage'], 'dc');
        if(empty($pagenavCategory['current'])) $pagenavCategory['current'] = 1;


        $pagenav = cot_pagenav('advert', $urlParams + array('dc' => $dcurl), $d, $totallines, $maxrowsperpage);
        if(empty($pagenav['current'])) $pagenav['current'] = 1;

        $breadcrumbs = '';
        if(!empty($crumbs)) $breadcrumbs = cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb'], true);

        $pageUrlParams = $urlParams;
        if($durl > 1) $pageUrlParams['d'] = $durl;

        $view = new View();
        $view->breadcrumbs = $breadcrumbs;
        $view->page_title = htmlspecialchars($category['title']);
        $view->category = $category;
        $view->subCategories = $subCategories;
        $view->advertisement = $advertisement;
        $view->totalitems = $totallines;
        $view->allowComments = $allowComments;
        $view->pagenav = $pagenav;
        $view->pagenavCategory = $pagenavCategory;
        $view->moderatorFilters = $moderatorFilters;
        $view->addNewUrl = $addNewUrl;
        $view->urlParams = $urlParams;
        $view->pageUrlParams = $pageUrlParams;

        /* === Hook === */
        foreach (cot_getextplugins('advert.list.view') as $pl) {
            include $pl;
        }
        /* ===== */

        return $view->render($template);
    }

    /**
     * Просмотр одного объявления
     */
    public function adView() {
        global $structure, $Ls;

        list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin']) = cot_auth('advert', 'any');
        cot_block(cot::$usr['auth_read']);

        $id = cot_import('id', 'G', 'INT');
        $al = cot_import('al', 'G', 'TXT');
        $c = cot_import('c', 'G', 'TXT');

        /* === Hook === */
        foreach (cot_getextplugins('advert.first') as $pl) {
            include $pl;
        }
        /* ===== */
        if(empty($id) && empty($al)) cot_die_message(404, TRUE);

        if(!empty($al)) {
            $advert = advert_model_Advert::fetchOne(array(array('alias', $al)));
        } else {
            $advert = advert_model_Advert::getById($id);
        }
        if(!$advert) cot_die_message(404, TRUE);

        list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin'], cot::$usr['auth_upload']) = cot_auth('advert',
            $advert->rawValue('category'), 'RWA1');
        cot_block(cot::$usr['auth_read']);

        $al = empty($advert->alias) ? '' : $advert->alias;
        $id = (int) $advert->id;

        $category = array('config' => array());
        if(isset($structure['advert'][$advert->rawValue('category')])) {
            $category = $structure['advert'][$advert->rawValue('category')];
            $category['config'] = cot::$cfg['advert']['cat_' . $advert->rawValue('category')];
        }
        $category['code'] = $advert->rawValue('category');

        cot::$sys['sublocation'] = $advert->title;

        if (($advert->state == advert_model_Advert::AWAITING_MODERATION
                || ($advert->state == advert_model_Advert::DRAFT)
                || ($advert->begin > cot::$sys['now'])
                || ($advert->expire > 0 && cot::$sys['now'] > $advert->expire))
            && (!$advert->canEdit()))
        {
            cot_log("Attempt to directly access an un-validated or future/expired advert", 'sec');
            cot_die_message(403, TRUE);
        }

        if (!cot::$usr['isadmin'] || cot::$cfg['advert']['count_admin']) {
            $advert->inc('views');
        }

        $title_params = array(
            'TITLE' => $advert->title,
            'CATEGORY' => $category['title']
        );
        cot::$out['subtitle'] = cot_title(cot::$cfg['page']['title_page'], $title_params);

        cot::$out['desc'] = $advert->description;
        cot::$out['keywords'] = strip_tags($category['config']['keywords']);

        // Building the canonical URL
        cot::$out['canonical_uri'] = $advert->url;

        $template = array('advert', $category['tpl']);

        if(!empty($advert->updated)) cot::$env['last_modified'] = strtotime($advert->updated);

        $allowComments = cot_plugin_active('comments');
        if($allowComments) {
            if(!isset(cot::$cfg['advert']['cat_'.$advert->category])) $allowComments = false;
            $allowComments = cot::$cfg['advert']['cat_'.$advert->category]['enable_comments'];
        }


        /* === Hook === */
        foreach (cot_getextplugins('advert.main') as $pl) {
            include $pl;
        }
        /* ===== */

        // Сообщение об истечении срока публикации
        $expDays = null;
        if ($advert->expire > 0 && $advert->state == advert_model_Advert::PUBLISHED){
            $diff = $advert->expire - cot::$sys['now'];

            $expDays = (floor($diff / 86400));

            if ($advert->canEdit()) {
                if (cot::$cfg['advert']['expNotifyPeriod'] > 0) {
                    if ($diff < (86400 * cot::$cfg['advert']['expNotifyPeriod']) && $diff > 0){
                        if ($expDays >= 1) {
                            cot_message(sprintf(cot::$L['advert_expire_soon'], cot_declension($expDays, $Ls['Days'], false, true)),
                                'warning');
                        }else{
                            cot_message(cot::$L['advert_expire_today'], 'warning');
                        }

                    }elseif ($diff <= 0){
                        cot_message(cot::$L['advert_expired'], 'warning');
                    }
                }
            }
        }

        // Если незарег может редактировать объявление, не кешировать эту страницу
        if (cot::$usr['id'] == 0 && !empty($_SESSION['advert']) && in_array($advert->id, $_SESSION['advert'])){
            cot::$cfg['cache_advert'] = cot::$cfg['cache_index'] = false;
        }

        $crumbs = cot_structure_buildpath('advert', $advert->category);
        if(cot::$cfg['advert']['firstCrumb']) array_unshift($crumbs, array(cot_url('advert'), cot::$L['advert_ads']));
        $crumbs[] = (!empty($advert->title)) ? $advert->title : cot::$L['advert_advert']." #".$advert->id;

        $urlParams = array('c' => $advert->category);
        if($advert->alias != '') {
            $urlParams['al'] = $advert->alias;
        } else {
            $urlParams['id'] = $advert->id;
        }

        $view = new View();
        $view->breadcrumbs = cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb'], true);
        $view->page_title = $advert->title;
        $view->advert = $advert;
        $view->category = $category;
        $view->allowComments = $allowComments;
        $view->daysLeft = $expDays;
        $view->urlParams = $urlParams;

        /* === Hook === */
        foreach (cot_getextplugins('advert.view') as $pl) {
            include $pl;
        }
        /* ===== */

        return $view->render($template);
    }

    public function editAction() {
        global $structure, $cot_extrafields, $db_structure;

        $id = cot_import('id', 'G', 'INT');           // id Объявления
        $c = cot_import('c', 'G', 'TXT');
        $act =  cot_import('act', 'G', 'ALP');
        if(empty($act))  $act =  cot_import('act', 'P', 'ALP');

        /* === Hook === */
        foreach (cot_getextplugins('advert.edit.first') as $pl)  {
            include $pl;
        }
        /* ===== */

        // Права на любую категорию доски объявлений
        list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin']) = cot_auth('advert', 'any');
        cot_block(cot::$usr['auth_write']);

        if (!$c || !isset($structure['advert'][$c])) {
            cot_die_message(404, TRUE);
        }

        $category = $structure['advert'][$c];
        $category['config'] = cot::$cfg['advert']['cat_' . $c];
        $category['code'] = $c;

        // Extra fields for structure
        foreach ($cot_extrafields[$db_structure] as $exfld) {
            $uname = $exfld['field_name'];
            $val = $structure['advert'][$c][$exfld['field_name']];
            $category[$uname.'_title'] = isset(cot::$L['structure_'.$exfld['field_name'].'_title']) ?
                cot::$L['structure_'.$exfld['field_name'].'_title'] : $exfld['field_description'];
            $category[$uname] = cot_build_extrafields_data('structure', $exfld, $val);
            $category[$uname.'_value'] = $val;
        }

        $published = 0;
        if(!$id){
            $advert = new advert_model_Advert();
            $advert->category = $c;
            $advert->user = cot::$usr['id'];

        }else{
            $advert = advert_model_Advert::getById($id);
            if(!$advert) cot_die_message(404, TRUE);
            if(!cot::$usr['isadmin']) {
                if ($advert->user != cot::$usr['id']) cot_die_message(404, TRUE);
            }
            if($c != $advert->category && isset($structure['advert'][$advert->category])) {
                $tmp = array('c' => $advert->category, 'a' => 'edit', 'id' => $advert->id);
                if(!empty($act)) $tmp['act'] = $act;
                cot_redirect(cot_url('advert', array('c' => $advert->category, 'a' => 'edit', 'id' => $advert->id), '', true));
            }

            if($act == 'clone') {
                $id = null;
                $advert = clone $advert;
                // Установить статус и пользователя нового объекта
                $advert->user = cot::$usr['id'];
                $advert->state = advert_model_Advert::DRAFT;
            }

            $published = ($advert->state < 2) ? 1 : 0;
        }

        if ($structure['advert'][$c]['locked'] && !cot::$usr['isadmin']) {
            cot_die_message(602, TRUE);

        } elseif($advert->id == 0) {
            // Если новое объявление, то проверим права на категорию:
            list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin'], cot::$usr['auth_upload']) = cot_auth('advert', $c, 'RWA1');

            // Если у пользователя нет прав на подачу объявления, то ищем категорию куда он может подать оьбъявление
            if(!cot::$usr['auth_write']) {
                foreach($structure['advert'] as $catCode => $catRow) {
                    $auth_write = cot_auth('advert', $catCode, 'W');
                    if($auth_write) cot_redirect(cot_url('advert', array('c' => $catCode, 'a' => 'edit'), '', true));
                }
            }
            cot_block(cot::$usr['auth_write']);
        }

        // Владелец объявления
        $user = array();
        if($advert->user > 0) {
            $user = cot_user_data($advert->user);
        }

        $periodItems = advert_periodItems($c);

        // Сохранение
        if($act == 'save') {
            unset($_POST['id'], $_POST['user']);

            cot_shield_protect();

            /* === Hook === */
            foreach (cot_getextplugins('advert.save.first') as $pl) {
                include $pl;
            }
            /* ===== */
            // импортировать даты
            $begin    = (int)cot_import_date('begin');
            $expire   = (int)cot_import_date('expire');
            if($begin == 0) {
                $begin = !empty($advert->begin) ? $advert->begin :  cot::$sys['now'];
            }

            // Пересчитать период публикации объявления
            if($expire == 0 && cot::$cfg['advert']['cat_' . $c]['maxPeriod'] > 0){
                $period = cot_import('period','P','INT');
                $maxPeriod = max($periodItems);
                if(empty($period)) $period = $maxPeriod;
                if(!cot::$usr['isadmin'] && $period > $maxPeriod) $period = $maxPeriod;
                if ($period > 0){
                    $expire = $begin + $period * 86400;
                }
            }

            if($category['config']['title_require']) {
                $advert->setValidator('title', 'required');
            }

            // Валидатор 'allowemptytext'
            if(!$category['config']['allowemptytext']) {
                $advert->setValidator('text', 'required');
            }

            if($category['config']['phone_require']) {
                // проверить надичие заполненного поля в профиле пользователя - владельца
                if(empty($user['user_phone'])) $advert->setValidator('phone', 'required');
            }

            if($category['config']['city_require']) {
                // Проверить наличие заполненного города (id или названия) в профиле владельца
                if(empty($user['user_city_name']) && empty($user['user_city'])) {
                    if(cot_plugin_active('regioncity')) {
                        $advert->setValidator('city', function($value) {
                            $value = (int)$value;
                            if($value == 0) return cot::$L['field_required'].': '.advert_model_Advert::getFieldLabel('city');
                            return true;
                        });
                    } else {
                        $advert->setValidator('city_name', 'required');
                    }
                }
            }

            if(cot::$usr['id'] == 0) {
                $advert->setValidator('person', 'required');

                // Email
                $email = cot_import('email', 'P', 'TXT');
                if(cot::$cfg['advert']['guestEmailRequire']) {
                    if ($email == '') cot_error(cot::$L['advert_err_noemail'], 'email');
                }
                if($email != '') {
                    $tmp = advert_checkEmail($email);
                    if($tmp !== true) cot_error($tmp, 'email');
                }

                // Капча
                if(cot::$cfg['advert']['guestUseCaptcha']) {
                    $verify  = cot_import('verify','P','TXT');
                    if (!cot_captcha_validate($verify)){
                        cot_error(cot::$L['captcha_verification_failed'], 'verify');
                    }
                }
            }

            $advert->setData($_POST);
            $advert->begin = $begin;
            $advert->expire = $expire;

            if(!cot::$usr['isadmin']) {
                if(!cot::$cfg['advert']['cat_'.$c]['allowSticky']) $advert->sticky = 0;
                if(cot::$usr['id'] == 0) $advert->sticky = 0; // гости не дают срочных объявлений
            }

            $advert->category = $c;
            if(empty($advert->user) || !cot::$usr['isadmin']) $advert->user = cot::$usr['id'];

            $published = cot_import('published', 'P', 'BOL');
            if(!$published) {
                $advert->state = advert_model_Advert::DRAFT;

            } elseif(cot::$usr['isadmin'] || cot_auth('advert', $c, '2')) {
                $advert->state = advert_model_Advert::PUBLISHED;

            } else {
                $advert->state = advert_model_Advert::AWAITING_MODERATION;
            }

            /* === Hook === */
            foreach (cot_getextplugins('advert.save.validate') as $pl) {
                include $pl;
            }
            /* ===== */

            // There is some errors
            if(!$advert->validate() || cot_error_found()) {
                $urlParams = array('c' => $c, 'a' => 'edit');
                if($advert->id > 0) $urlParams['id'] = $advert->id;
                cot_redirect(cot_url('advert', $urlParams, '', true));
            }

            if(empty($advert->sort)) $advert->sort = cot::$sys['now'];

            $isNew = ($advert->id == 0);

            // Сохранение
            if($advert->save()) {
                // Для незарега запомним id страницы для чтого, чтобы он мог ее отредактировать в пределах сесии
                if ($isNew) {
                    if(cot::$usr['id'] == 0) {
                        if (empty($_SESSION['advert'])) $_SESSION['advert'] = array();
                        if (!in_array($id, $_SESSION['advert'])) $_SESSION['advert'][] = $advert->id;
                    }
                    if($advert->state == advert_model_Advert::PUBLISHED) {
                        cot_message(cot::$L['advert_created']);
                    }
                } else {
                    if($advert->state == advert_model_Advert::PUBLISHED) {
                        cot_message(cot::$L['advert_updated']);
                    }
                }

                if($advert->state == advert_model_Advert::AWAITING_MODERATION) {
                    cot_message(cot::$L['advert_awaiting_moderation']);

                } elseif($advert->state == advert_model_Advert::DRAFT) {
                    cot_message(cot::$L['Saved']);
                }

                $redirectUrl = $advert->getUrl(true);

                /* === Hook === */
                foreach (cot_getextplugins('advert.save.done') as $pl) {
                    include $pl;
                }
                /* ===== */

                // Редирект на станицу объявления
                cot_redirect($redirectUrl);
            }
        }

        $crumbs = cot_structure_buildpath('advert', $c);
        if(cot::$cfg['advert']['firstCrumb']) array_unshift($crumbs, array(cot_url('advert'), cot::$L['advert_ads']));

        if(!$id){
            $crumbs[] = $title = cot::$L['advert_add_new'];
            cot::$out['subtitle'] = $title;

        }else{
            $crumbs[] = array($advert->url, $advert->title);
            $crumbs[] = cot::$L['Edit'];
            $title = cot::$L['advert_advert'].' #'.$advert->id;
            if(!empty($advert->title)) $title = $advert->title;
            $title .= ': '.cot::$L['Edit'];
            if(!empty(cot::$out['subtitle'])) $title .= ' - '.cot::$out['subtitle'];
            cot::$out['subtitle'] = $title;
        }

        // Elemets placeholders
        $placeHolder_Person = '';
        $placeHolder_Phone = '';
        $placeHolder_Email = '';
        $placeHolder_City = '';
        //if($advert->user == cot::$usr['id'] && cot::$usr['id'] > 0) {
        if(!empty($user)) {
            // Контакное лицо
            $placeHolder_Person = cot_user_full_name($user);

            // Телефон
            if(!empty($user['user_phone'])) $placeHolder_Phone = $user['user_phone'];

            // email
            if(!$user['user_hideemail'])  $placeHolder_Email = $user['user_email'];

            // город
            if(!empty($user['user_city_name'])) $placeHolder_City = $user['user_city_name'];
        }

        // 'input_textarea_editor', 'input_textarea_medieditor', 'input_textarea_minieditor', ''
        $editor = 'input_textarea_editor';

        /* === Hook === */
        foreach (cot_getextplugins('advert.edit.main') as $pl) {
            include $pl;
        }
        /* ===== */

        $minYear = date('Y');
        $maxYear = $minYear + 30;
        $price = $advert->rawValue('price');
        if($price <= 0) $price = '';
        $formElements = array(
            'hidden' => array(
                'element' => cot_inputbox('hidden', 'act', 'save')
            ),
            'category' => array(
                'element' => cot_selectbox_structure('advert', $advert->category, 'category'),
                'title' => $advert->getFieldLabel('category')
            ),
            'price' => array(
                'element' => cot_inputbox('text', 'price', $price),
                'title' => $advert->getFieldLabel('price'),
                'hint' => cot::$L['advert_price_hint'],
            ),
            'title' => array(
                'element' => cot_inputbox('text', 'title', $advert->rawValue('title')),
                'required' => true,
                'title' => $advert->getFieldLabel('title')
            ),
            'description' => array(
                'element' => cot_inputbox('text', 'description', $advert->rawValue('description')),
                'title' => $advert->getFieldLabel('description')
            ),
            'text' => array(
                'element' => cot_textarea('text', $advert->rawValue('text'), 5, 120, '', $editor),
                'title' => $advert->getFieldLabel('text')
            ),
            'person' => array(
                'element' => cot_inputbox('text', 'person', $advert->rawValue('person'),
                    array('class' => 'form-control', 'placeholder' => $placeHolder_Person)),
                'title' => $advert->getFieldLabel('person'),
                'required' => (cot::$usr['id'] == 0),
            ),
            'email' => array(
                'element' => cot_inputbox('text', 'email', $advert->rawValue('email'),
                    array('class' => 'form-control', 'placeholder' => $placeHolder_Email)),
                'title' => $advert->getFieldLabel('email')
            ),
            'city' => array(
                'element' => cot_inputbox('text', 'city_name', $advert->rawValue('city_name'),
                    array('class' => 'form-control', 'placeholder' => $placeHolder_City)),
                'title' => $advert->getFieldLabel('city_name'),
                'required' => $category['config']['city_require']
            ),
            'phone' => array(
                'element' => cot_inputbox('text', 'phone', $advert->rawValue('phone'),
                    array('class' => 'form-control', 'placeholder' => $placeHolder_Phone)),
                'title' => $advert->getFieldLabel('phone'),
                'required' => $category['config']['phone_require']
            ),
            'sticky' => array(
                'element' => cot_checkbox($advert->sticky, 'sticky', $advert->getFieldLabel('sticky')),
                'label' => $advert->getFieldLabel('sticky')
            ),
            'published' => array(
                'element' => cot_checkbox($published, 'published', cot::$L['advert_published'].'?'),
                'label' => cot::$L['advert_published'].'?'
            ),
            'begin' => array(
                'element' => cot_selectbox_date($advert->begin, 'long','begin', $maxYear, $minYear),
                'title' => $advert->getFieldLabel('begin')
            ),
            'expire' => array(
                'element' => cot_selectbox_date($advert->expire, 'long','expire', $maxYear, $minYear),
                'title' => $advert->getFieldLabel('expire')
            ),
            'sort' => array(
                'element' => cot_selectbox_date($advert->sort, 'long','sort', $maxYear, $minYear),
                'title' => $advert->getFieldLabel('sort')
            ),
            'period' => array(
                'element' => cot_selectbox('', 'period', $periodItems, array(), false),
                'title' => cot::$L['advert_period']
            ),
        );
        if(!empty($cot_extrafields[cot::$db->advert])) {
            // Extra fields for ads
            foreach ($cot_extrafields[cot::$db->advert] as $exfld) {
                $fName = $exfld['field_name'];
                $formElements[$fName] = array(
                    'element' => cot_build_extrafields($fName, $exfld, $advert->rawValue($fName)),
                    'title' => isset(cot::$L['advert_'.$exfld['field_name'].'_title']) ?
                        cot::$L['advert_'.$exfld['field_name'].'_title'] : $advert->getFieldLabel($fName)
                );
            }
        }

        if(cot_plugin_active('regioncity')) {
            $formElements['city']['element'] = rec_select2_city('city', $advert->rawValue('city'), true,
                array('class' => 'form-control', 'placeholder' => $placeHolder_City));
        }

        if($category['config']['city_require']) {
            $formElements['city']['required'] = true;
        }

        if($category['config']['phone_require']) {
            $formElements['phone']['required'] = true;
        }

        // Hints
        if(!empty($user)) {
            // Контакное лицо
            $formElements['person']['hint'] = cot::$L['advert_leave_empty_to_use'].": ".cot_user_full_name($user);

            // Телефон
            if(!empty($user['user_phone'])) {
                $formElements['phone']['hint'] = cot::$L['advert_leave_empty_to_use'].": ".$user['user_phone'];
            }

            // email
            if(!$user['user_hideemail']) {
                $formElements['email']['hint'] = cot::$L['advert_leave_empty_to_use'].": ".$user['user_email'];
            }

            // город
            if(!empty($user['user_city_name'])) {
                $formElements['city']['hint'] = cot::$L['advert_leave_empty_to_use'].": ".$user['user_city_name'];
            }
        }


        if(!cot::$usr['isadmin']) {
            unset($formElements['begin']);
            unset($formElements['expire']);
            unset($formElements['sort']);

            if(cot::$usr['id'] == 0) {
                if(cot::$cfg['advert']['guestEmailRequire']) {
                    $formElements['email']['required'] = true;
                }
                // Гости не дают срочных объявлений
                unset($formElements['sticky']);

                // Капча
                if(cot::$cfg['advert']['guestUseCaptcha']) {
                    $formElements['verify'] = array(
                        'element' => cot_inputbox('text', 'verify'),
                        'img' => cot_captcha_generate(),
                        'title' => cot::$L['advert_captcha'],
                        'required' => true,
                    );
                }
            }
            if(!cot::$cfg['advert']['cat_'.$c]['allowSticky'] && isset($formElements['sticky'])) unset($formElements['sticky']);

        } else {
            // Администратор напрямую указывает дату окончания публикации
            unset($formElements['period']);
        }

        $actionParams = array(
            'a' => 'edit',
            'c' => $advert->category
        );
        if($advert->id > 0) $actionParams['id'] = $advert->id;

        $view = new View();
        $view->breadcrumbs = cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb'], true);
        $view->page_title = $title;
        $view->category = $category;
        $view->advert = $advert;
        $view->user = $user;
        $view->formElements = $formElements;
        $view->formAction = cot_url('advert', $actionParams);

        /* === Hook === */
        foreach (cot_getextplugins('advert.edit.view') as $pl) {
            include $pl;
        }
        /* ===== */

        return $view->render(array('advert', 'edit', $structure['advert'][$c]['tpl']));
    }

    /**
     * Утвердить / отправить на модерацию
     */
    public function validateAction() {
        $id = cot_import('id', 'G', 'INT');         // id Объявления
        $b  = cot_import('b', 'G', 'HTM');          // Куда вернуться

        /* === Hook === */
        foreach (cot_getextplugins('advert.validate.first') as $pl)  {
            include $pl;
        }
        /* ===== */

        // Права на любую категорию доски объявлений
        list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin']) = cot_auth('advert', 'any');
        cot_block(cot::$usr['isadmin']);

        $advert = advert_model_Advert::getById($id);
        if(!$advert) cot_die_message(404, TRUE);

        if(!cot::$usr['isadmin']) {
            if ($advert->user != cot::$usr['id']) cot_die_message(404, TRUE);
        }

        cot::$usr['isadmin_local'] = cot_auth('advert', $advert->category, 'A');
        cot_block(cot::$usr['isadmin_local']);

        $title = $advert->title;
        $userId = $advert->user;

        if($advert->state == advert_model_Advert::PUBLISHED) {
            $advert->state = advert_model_Advert::AWAITING_MODERATION;
            $msg = cot::$L['advert_unvalidated'];

        } else {
            $advert->state = advert_model_Advert::PUBLISHED;
            if($advert->begin < cot::$sys['now']) $advert->begin = cot::$sys['now'];

            // Пересчитать период публикации объявления
            if($advert->expire <= cot::$sys['now'] && cot::$cfg['advert']['cat_' . $advert->category]['maxPeriod'] == 0) {
                $advert->expire = 0;
            }

            if($advert->expire <= cot::$sys['now'] && cot::$cfg['advert']['cat_' . $advert->category]['maxPeriod'] > 0){

                $periodItems = advert_periodItems($advert->category);
                $period = max($periodItems);

                if ($period > 0){
                    $advert->expire = $advert->begin + $period * 86400;
                }
            }

            $msg = cot::$L['advert_validated'];
        }

        $advert->save();

        /* === Hook === */
        foreach (cot_getextplugins('advert.validate.done') as $pl)  {
            include $pl;
        }
        /* ===== */

        if(!empty($b)) {
            $b = unserialize(base64_decode($b));

        } elseif(!empty($_SESSION['cot_com_back']) && !empty($_SESSION['cot_com_back']['advert'])) {
            $b = $_SESSION['cot_com_back']['advert'];
            unset($_SESSION['cot_com_back']['advert']);
        }

        cot_message($msg);

        if (empty($b)) {
            cot_redirect($advert->getUrl(true));
        }

        cot_redirect(cot_url('advert', $b, '', true));
    }

    public function deleteAction() {
        $id = cot_import('id', 'G', 'INT');         // id Объявления
        $b  = cot_import('b', 'G', 'HTM');          // Куда вернуться

        /* === Hook === */
        foreach (cot_getextplugins('advert.delete.first') as $pl)  {
            include $pl;
        }
        /* ===== */

        // Права на любую категорию доски объявлений
        list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin']) = cot_auth('advert', 'any');
        cot_block(cot::$usr['auth_write']);

        $advert = advert_model_Advert::getById($id);
        if(!$advert) cot_die_message(404, TRUE);

        if(!cot::$usr['isadmin']) {
            if ($advert->user != cot::$usr['id']) cot_die_message(404, TRUE);
        }

        $title = $advert->title;
        $userId = $advert->user;

        $advert->delete();

        /* === Hook === */
        foreach (cot_getextplugins('advert.delete.done') as $pl)  {
            include $pl;
        }
        /* ===== */

        if(!empty($b)) {
            $b = unserialize(base64_decode($b));

        } elseif(!empty($_SESSION['cot_com_back']) && !empty($_SESSION['cot_com_back']['advert'])) {
            $b = $_SESSION['cot_com_back']['advert'];
            unset($_SESSION['cot_com_back']['advert']);
        }

        if (empty($b)) {
            $b = array('m' => 'user');
            if ($userId != cot::$usr['id']) $b['uid'] = $userId;
        }

        cot_message(sprintf(cot::$L['advert_deleted'], $title));
        cot_redirect(cot_url('advert', $b, '', true));
    }
}

