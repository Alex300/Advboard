<?php
/**
 * Ads board module for Cotonti Siena
 *
 * @package Advert
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) 2015 Portal30 Studio http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL.');

class advert_controller_Widget
{
    public static function adsList($condition = array(), $tpl = 'advert.widget.list', $items = 0, $order = '',
                                   $onlyActive = true, $pagination = 'pld', $params = array()) {

        // Get pagination number if necessary
        if (!empty($pagination)) {
            list($pg, $d, $durl) = cot_import_pagenav($pagination, $items);

        } else {
            $d = 0;
        }

        if(empty($condition)) $condition = array();

        if($onlyActive) {
            $condition[] = array('begin', cot::$sys['now'], '<=');
            $condition[] = array('SQL', "expire = 0 OR expire > ".cot::$sys['now']);
            $condition[] = array('state', advert_model_Advert::PUBLISHED);
        }

        if(empty($order)) {
            $order = array(
                array('state', 'ASC'),
                array('sort', 'DESC'),
            );
        }

        /* === Hook === */
        foreach (cot_getextplugins('advert.widget.list.query') as $pl) {
            include $pl;
        }
        /* ===== */

        $totallines = advert_model_Advert::count($condition);
        $advertisement = advert_model_Advert::find($condition, $items, $d, $order);

        // Render pagination
        if(empty($params['module'])) {
            $params['module'] = defined('COT_PLUG') ? 'plug' : cot::$env['ext'];
        }

        if(empty($params['urlParams'])) {
            if (defined('COT_LIST')) {
                global $list_url_path;
                $params['urlParams'] = $list_url_path;

            } elseif (defined('COT_PAGES')) {
                global $al, $id, $pag;
                $params['urlParams'] = empty($al) ? array('c' => $pag['page_cat'], 'id' => $id) : array('c' => $pag['page_cat'], 'al' => $al);

            } else {
                $params['urlParams'] = array();
            }
        }

        if(empty($params['ajax'])) $params['ajax'] = false;
        if(empty($params['target_div'])) $params['target_div'] = '';
        if(empty($params['ajax_module'])) $params['ajax_module'] = '';
        if(empty($params['ajax_params'])) $params['ajax_params'] = array();

        $pagenav = cot_pagenav($params['module'], $params['urlParams'], $d, $totallines, $items, $pagination, '', $params['ajax'],
            $params['target_div'], $params['ajax_module'], $params['ajax_params']);

        if(empty($pagenav['current'])) $pagenav['current'] = 1;


        $view = new View();

        /* === Hook === */
        foreach (cot_getextplugins('advert.widget.list.view') as $pl) {
            include $pl;
        }
        /* ===== */

        $view->advertisement = $advertisement;
        $view->totalitems = $totallines;
        $view->pagenav = $pagenav;

        return $view->render($tpl);
    }

    public static function compare() {
        $totallines = 0;
        if(!empty($_SESSION['advert_compare'])) $totallines = count($_SESSION['advert_compare'][cot::$sys['site_id']]);

        $tpl = array('advert', 'widget', 'compare', );

        $view = new View();
        $view->advertisement = $_SESSION['advert_compare'][cot::$sys['site_id']];
        $view->totalitems = $totallines;

        return $view->render($tpl);
    }
}