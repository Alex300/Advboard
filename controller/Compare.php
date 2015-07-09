<?php
defined('COT_CODE') or die('Wrong URL.');


/**
 * Ads board module for Cotonti Siena
 *     Compare class
 *
 * @package Advboard
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */
class advboard_controller_Compare
{
    public function indexAction() {

        Resources::linkFileFooter(cot::$cfg['modules_dir'].'/advboard/js/advboard.compare.js');

        $sort = cot_import('s', 'G', 'ALP');       // order field name
        $way = cot_import('w', 'G', 'ALP', 4);    // order way (asc, desc)
        //$maxrowsperpage = cot::$cfg['advboard']['cat___default']['maxrowsperpage'];
        $maxrowsperpage = 0;

        /* === Hook === */
        foreach (cot_getextplugins('advboard.compare.first') as $pl) {
            include $pl;
        }
        /* ===== */

        $sort = empty($sort) ? cot::$cfg['advboard']['cat___default']['order'] : $sort;
        $way = (empty($way) || !in_array($way, array('asc', 'desc'))) ? cot::$cfg['advboard']['cat___default']['way'] : $way;

        $canonicalUrlParams = array('m' => 'compare');

        $where = array();
        if(!empty($_SESSION['advboard_compare']) && !empty($_SESSION['advboard_compare'][cot::$sys['site_id']])) {
            $where['id'] = array('id', array_keys($_SESSION['advboard_compare'][cot::$sys['site_id']]));
            $where['state'] = array('state', advboard_model_Advert::PUBLISHED);
            $where['begin'] = array('begin', cot::$sys['now'], '<=');
            $where['expire'] = array('SQL', "expire = 0 OR expire > ".cot::$sys['now']);
        }

        $template = array('advboard', 'compare');

        cot::$out['subtitle'] = cot::$L['advboard_compare'];

        // Building the canonical URL
        cot::$out['canonical_uri'] = cot_url('advboard', $canonicalUrlParams);

        $condition = array();
        foreach($where as $key => $val) {
            $condition[] = $val;
        }

        $order = array(array('sticky', 'desc'), array($sort, $way));

        /* === Hook === */
        foreach (cot_getextplugins('advboard.compare.query') as $pl) {
            include $pl;
        }
        /* ===== */

        $advertisement = null;
        $totallines = 0;
        if(!empty($condition)) {
            $totallines = advboard_model_Advert::count($condition);
            if ($totallines > 0) $advertisement = advboard_model_Advert::find($condition, $maxrowsperpage, 0, $order);
        }


        /* === Hook === */
        foreach (cot_getextplugins('advboard.compare.main') as $pl) {
            include $pl;
        }
        /* ===== */

        $crumbs = array();
        if (cot::$cfg['advboard']['firstCrumb']) $crumbs[] = array(cot_url('advboard'), cot::$L['advboard_ads']);
        $crumbs[] = array(cot_url('advboard', array('m' => 'compare')), cot::$L['advboard_compare']);

        $breadcrumbs = '';
        if(!empty($crumbs)) $breadcrumbs = cot_breadcrumbs($crumbs, cot::$cfg['homebreadcrumb'], true);

//        $pageUrlParams = $urlParams;
//        if($durl > 1) $pageUrlParams['d'] = $durl;

        $view = new View();
        $view->breadcrumbs = $breadcrumbs;
        $view->page_title = htmlspecialchars(cot::$L['advboard_compare']);
        $view->advertisement = $advertisement;
        $view->totalitems = $totallines;
//        $view->urlParams = $urlParams;
//        $view->pageUrlParams = $pageUrlParams;

        /* === Hook === */
        foreach (cot_getextplugins('advboard.compare.view') as $pl) {
            include $pl;
        }
        /* ===== */

        return $view->render($template);
    }

    public function deleteAction() {
        $ids = null;
        if(isset($_GET['ids'])){
            if(is_array($_GET['ids'])){
                $ids = cot_import('ids', 'G', 'ARR');
                $ids = array_unique(array_map('intval', $ids));

            }elseif(is_int($_GET['ids']) || ctype_digit($_GET['ids']) ){
                $tmp = cot_import('ids', 'G', 'INT');
                if($tmp) $ids = array($tmp);

            }elseif($_GET['ids'] == 'all'){
                unset($_SESSION['advboard_compare']);
            }
        }

        if($ids){
            foreach($ids as $id){
                if(!empty($_SESSION['advboard_compare'])){
                    if(!empty($_SESSION['advboard_compare'][cot::$sys['site_id']][$id])){
                        unset($_SESSION['advboard_compare'][cot::$sys['site_id']][$id]);
                        $ret['removedIds'][] = $id;
                    }
                    if(!empty($_SESSION['advboard_compare'][cot::$sys['site_id']])){
                        $ret['count'] = count($_SESSION['advboard_compare'][cot::$sys['site_id']]);
                        if($ret['count'] == 0){
                            unset($_SESSION['advboard_compare']);
                            break;
                        }
                    }
                }else{
                    break;
                }
            }
        }

        $urlParams = array('m' => 'compare');

        cot_redirect(cot_url('advboard', $urlParams, '', true));
        exit();
    }

    public function ajxAddAction() {
        $ret = array('error' => '', 'added' => array(), 'addedIds' => array(), 'removedIds' => array());

        $ids = null;
        if(isset($_POST['ids'])){
            if(is_array($_POST['ids'])){
                $ids = cot_import('ids', 'P', 'ARR');
                $ids = array_unique(array_map('intval', $ids));

            }elseif(is_int($_POST['ids']) || ctype_digit($_POST['ids']) ){
                $tmp = cot_import('ids', 'P', 'INT');
                if($tmp) $ids = array($tmp);
            }
        }
        if(!$ids){
            $ret['error'] = cot::$L['advboard_not_found'];
            echo json_encode($ret);
            exit();
        }

        $advertisement = advboard_model_Advert::find(array(array('id',$ids)));
        if(!$advertisement){
            $ret['error'] = cot::$L['advboard_not_found'];
            echo json_encode($ret);
            exit();
        }

        foreach($advertisement as $advRow){
            if($tmp = $this->addToCompare($advRow)){
                $ret['added'][] = $tmp;
            }

        }
        $ret['count'] = count($_SESSION['advboard_compare'][cot::$sys['site_id']]);

        echo json_encode($ret);
        exit();
    }

    public function ajxDeleteAction() {
        $ret = array('error' => '', 'added' => array(), 'addedIds' => array(), 'removedIds' => array());

        $ids = null;
        if(isset($_POST['ids'])){
            if(is_array($_POST['ids'])){
                $ids = cot_import('ids', 'P', 'ARR');
                $ids = array_unique(array_map('intval', $ids));

            }elseif(is_int($_POST['ids']) || ctype_digit($_POST['ids']) ){
                $tmp = cot_import('ids', 'P', 'INT');
                if($tmp) $ids = array($tmp);

            }elseif($_POST['ids'] == 'all'){
                $ret['count'] = 0;
                unset($_SESSION['advboard_compare']);
                echo json_encode($ret);
                exit();
            }
        }

        if(!$ids){
            $ret['error'] = cot::$L['advboard_not_found'];
            echo json_encode($ret);
            exit();
        }

        $ret['count'] = 0;
        foreach($ids as $id){
            if(!empty($_SESSION['advboard_compare'])){
                if(!empty($_SESSION['advboard_compare'][cot::$sys['site_id']][$id])){
                    unset($_SESSION['advboard_compare'][cot::$sys['site_id']][$id]);
                    $ret['removedIds'][] = $id;
                }
                if(!empty($_SESSION['advboard_compare'][cot::$sys['site_id']])){
                    $ret['count'] = count($_SESSION['advboard_compare'][cot::$sys['site_id']]);
                    if($ret['count'] == 0){
                        unset($_SESSION['advboard_compare']);
                        break;
                    }
                }
            }else{
                break;
            }
        }

        if($ret['count'] == 0) unset($_SESSION['advboard_compare']);

        echo json_encode($ret);
        exit();
    }


    // ==== Служебные методы ====
    /**
     * Добавить объявление к сравнению
     * @param advboard_model_Advert $advert
     */
    protected function addToCompare($advert){
        global $Ls;

        $desc = $advert->description;

        $price = '';
        if(!empty($advert->price)) $price = $advert->price;

        if(empty($_SESSION['advboard_compare']) || empty($_SESSION['advboard_compare'][cot::$sys['site_id']][$advert->id])){
            //$ret['addedIds'][] = $advboard->id;
        }

        $_SESSION['advboard_compare'][cot::$sys['site_id']][$advert->id] = array(
            'id' => $advert->id,
            'title' => $advert->title,
            'price' => $price,
            'priceFormatted' => ($price != '') ? number_format($price, 0, '.', ' ') : '',
            'url' => $advert->url,
            'description' => $desc
        );

        return $_SESSION['advboard_compare'][cot::$sys['site_id']][$advert->id];
    }
}

