<?php
/**
 * Ads board module for Cotonti Siena
 *
 * @package Advert
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL.');

cot::$db->registerTable('advert');
cot_extrafields_register_table('advert');

// Requirements
require_once cot_langfile('advert', 'module');
require_once  cot_incfile('advert', 'module', 'resources');

if(cot_module_active('pm')) require_once  cot_incfile('pm', 'module');

/**
 * Returns possible values for category sorting order
 */
function cot_advert_config_order() {
    global $cot_extrafields, $L, $db_advert;

    $options_sort = array(
        'sort' => cot::$L['advert_by_sort_field'],
        'id' => cot::$L['Id'],
        'title' => cot::$L['Title'],
        'desc' => cot::$L['Description'],
        'text' => cot::$L['Body'],
        'price' => cot::$L['advert_price'],
        'user' => cot::$L['Owner'],
        'begin' => cot::$L['Begin'],
        'expire' => cot::$L['Expire'],
        'views' => cot::$L['Count'],
        'created' => cot::$L['Date'],
        'updated' => cot::$L['Updated'],
    );

    if(!empty($cot_extrafields[$db_advert])) {
        foreach ($cot_extrafields[$db_advert] as $exfld) {
            $options_sort[$exfld['field_name']] = isset(cot::$L['advert_' . $exfld['field_name'] . '_title']) ?
                cot::$L['advert_' . $exfld['field_name'] . '_title'] : $exfld['field_description'];
        }
    }

    cot::$L['cfg_order_params'] = array_values($options_sort);
    return array_keys($options_sort);
}


/**
 * Recalculates page category counters
 *
 * @param string $cat Cat code
 * @return int
 */
function cot_advert_sync($cat) {
//    $sql = cot::$db->query("SELECT COUNT(*) FROM cot::$db->advert
//		WHERE category=".cot::$db->quote($cat)." AND (state = 0 OR page_state=2)");

//    $sql = cot::$db->query("SELECT COUNT(*) FROM ".cot::$db->advert."
//            WHERE category=".cot::$db->quote($cat)." AND (state = 0)");
//
//    return (int) $sql->fetchColumn();

    // Наверное не учитываем состояние объявления, а считаем все.
    return advert_model_Advert::count(array(array('category', $cat)));
}

/**
 * Update page category code
 *
 * @param string $oldcat Old Cat code
 * @param string $newcat New Cat code
 * @return bool
 */
function cot_advert_updatecat($oldcat, $newcat) {
    return (bool) cot::$db->update(cot::$db->advert, array("category" => $newcat), "category=?", $oldcat);
}

/**
 * Returns permissions for a page category.
 * @param  string $cat Category code
 * @return array       Permissions array with keys: 'auth_read', 'auth_write', 'isadmin', 'auth_download'
 * @todo Реализуй меня
 */
function cot_advert_auth($cat = null) {
    if (empty($cat)) $cat = 'any';
    $auth = array();
    list($auth['auth_read'], $auth['auth_write'], $auth['isadmin'], $auth['auth_download']) = cot_auth('advert', $cat, 'RWA1');
    return $auth;
}

if(!function_exists('cot_formGroupClass')) {
    /**
     * Класс для элемента формы
     * @param $name
     * @return string
     */
    function cot_formGroupClass($name){
        global $cfg;

        $error = $cfg['msg_separate'] ? cot_implode_messages($name, 'error') : '';
        if($error) return 'has-error';

        return '';
    }
}


if(!function_exists('array_insert')) {
    /**
     * Insert new element into $array in $position
     * @param array $array
     * @param int|string $offset Insert position.
     *                   Если передан строковый ключ, то новый элемент будет вставлен после элемента с этим ключом
     * @param mixed $insert
     */
    function array_insert (&$array, $offset, $insert) {
        $insert = (array) $insert;

        $keys = array_flip(array_keys($array));

        if (isset($array[$offset]) && is_string($offset)) {
            $offset = $keys[$offset] + 1;
        }

        $first_array = array_splice ($array, 0, $offset);
        $array = array_merge ($first_array, $insert, $array);
    }
}

// ========================================================

/**
 * Количество объявлений пользователя
 * @param int $uid
 * @param bool $cache
 * @return int
 */
function cot_user_ads_count($uid = 0, $cache = true) {

    static $stCache = array();

    if(empty($uid)) $uid = cot::$usr['id'];

    if($cache && isset($stCache[$uid])) return $stCache[$uid];

    $cond = array(array('user', $uid));
    $stCache[$uid] = advert_model_Advert::count($cond);

    return $stCache[$uid];
}

/**
 * Проверяем e-mail
 * @param string $mail - проверяемый e-mail
 *
 * @return bool|string TRUE or Error message
 */
function advert_checkEmail($mail = ''){
    global $db_banlist, $db, $L;

    // Проверяем бан-лист
    if (cot_plugin_active('banlist')){
        $sql = cot::$db->query("SELECT banlist_reason, banlist_email FROM $db_banlist
            WHERE banlist_email LIKE ".cot::$db->quote('%'.$mail.'%'));
        if ($row = $sql->fetch()) {
            $ret = cot::$L['aut_emailbanned']. $row['banlist_reason'];
            return $ret;
        }
        $sql->closeCursor();
    }

    if(!cot_check_email($mail)){
        $ret = cot::$L['advert_err_wrongmail'];
        return $ret;
    }

    return true;
}

/**
 * Чекбокс "Добавить к сравнению
 *
 * @param advert_model_Advert $item
 * @param string $title
 * @return string
 */
function advert_compare_checkbox($item, $title = null) {

    static $loaded = false;

    $choosen = false;

    if($item instanceof advert_model_Advert) {
        $id = $item->id;
    } else {
        $id = $item;
    }

    if($id == 0) return '';

    if(!empty($_SESSION['advert_compare']) && !empty($_SESSION['advert_compare'][cot::$sys['site_id']])) {
        if(isset($_SESSION['advert_compare'][cot::$sys['site_id']][$id])
            && !empty($_SESSION['advert_compare'][cot::$sys['site_id']][$id])) {

            $choosen = true;
        }
    }

    if(is_null($title)) $title = cot::$L['advert_compare_add'];

    $ret = cot_checkbox($choosen, 'advert_comp[]', $title, array('class' => 'advert_compare'), $id, 'input_check');

    if(!$loaded) {
        Resources::linkFileFooter(cot::$cfg['modules_dir'].'/advert/js/advert.compare.form.js');
        $loaded = true;
        $ret .= cot_xp();
    }

    return $ret;
}

/**
 * Рендерит строку для сравнения объявлений
 *
 * Используется в шаблоне страницы сравнения
 * В Вашем шаблоне, возможно нужно будет использовать свою функцию
 *
 * @param advert_model_Advert[] $compare
 * @param $field
 * @param string|array $params - Название или Массив параметров поля:
 *                                              'title'   => название поля
 *                                              'prefix'  => префикс
 *                                              'postfix' => постфикс
 * @return string
 */
function advert_compare_renderRow($compare, $field, $params = array()){

    $predifined = array();

    static $modelFields = false;
    static $counter = 0;

    if(empty($modelFields)) $modelFields = advert_model_Advert::getFields();

//    if(empty($modelFields[$field]) && !in_array($field, $predifined)) return '';


    $ret = '';
    $found = false;
    $tmpVal = '';
    $style = '';
    if($counter == 0) $style = ' style="width: 130px"';
    foreach($compare as $item){
        $val = $item->$field;
//        var_dump($val);
        if(!empty($val)) $found = true;

        // Обработка значения
        if(isset($modelFields[$field])) {
            switch($modelFields[$field]['type']){
                case 'tinyint':
                case 'bool':
                    $val = advert_YesNo(intval($val));
                    break;

                case 'int':
                case 'double':
                    $val = ($val != 0) ? $val : '';
                    break;

                case 'varchar':
                    $val = ($val != '') ? htmlspecialchars($val) : '';
                    break;
            }

            if(!in_array($modelFields[$field]['type'], array('tinyint', 'bool'))){
                if(isset(cot::$L[$field.'_'.$val])){
                    $val = cot::$L[$field.'_'.$val];
                }
            }
        }

        if($val != '') {
            if ($field == 'price') $val = number_format($val, 0, ',', ' ');
            if (isset($params['prefix'])) $val = $params['prefix'] . $val;
            if (isset($params['postfix'])) $val .= $params['postfix'];
        }
        $tmpVal .= '<td class="text-center"'.$style.'>'.$val.'</td>';
    }

    if(!$found) return '';

    if(is_string($params)) $params = array('title' => $params);

    $params['title'] = isset($params['title']) ? $params['title'] : "";
    if(empty($params['title']) && isset($modelFields[$field]['description']))  $params['title'] = $modelFields[$field]['description'];

    $style = '';
    if($counter == 0) $style = ' style="width: 200px"';

    $ret = '<tr><td class="text-right"'.$style.'>'.htmlspecialchars($params['title'])."</td>{$tmpVal}<td style=\"border: none\"></td></tr>";

    $counter++;

    return $ret;
}

/**
 * Элементы списка для выбора периода размещения объявления
 */
function advert_periodItems($c = ''){

    $maxPeriod = cot::$cfg['advert']['cat___default']['maxPeriod'];
    if(!empty($c) && isset(cot::$cfg['advert']['cat_' . $c])) $maxPeriod = cot::$cfg['advert']['cat_' . $c]['maxPeriod'];

    $period = array();
    $tmp = 0;
    while ($tmp <= $maxPeriod) {
        if ($tmp < 10) $tmp += 1;
        elseif ($tmp < 10) $tmp += 1;
        elseif ($tmp < 20) $tmp += 2;
        elseif ($tmp < 30) $tmp += 5;
        elseif ($tmp < 90) $tmp += 10;

        if ($tmp <= $maxPeriod) $period[] = $tmp;
    }

    if ($tmp < $maxPeriod) $period[] = $maxPeriod;

    if (cot::$cfg['advert']['periodOrder'] == 'desc') rsort($period);

    return $period;
}

function advert_relative2absolute($matches) {
    global $sys;
    $res = $matches[1].$matches[2].'='.$matches[3];
    if (preg_match('#^(http|https|ftp)://#', $matches[4])) {
        $res .= $matches[4];

    } else {
        if ($matches[4][0] == '/') {
            $scheme = $sys['secure'] ? 'https' : 'http';
            $res .= $scheme . '://' . $sys['host'] . $matches[4];

        } else {
            $res .= COT_ABSOLUTE_URL . $matches[4];
        }
    }
    $res .= $matches[5];
    return $res;
}

function advert_YesNo($cond){
    if($cond) return '<span class="text-success glyphicon glyphicon-ok"></span>';

    return '<span class="glyphicon glyphicon-minus"></span>';
}