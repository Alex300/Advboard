<?php
/**
 * Ads board module for Cotonti Siena
 *     Uninstallation handler
 *
 * @package Advboard
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL');

global $db_com;

// Удалить все файлы и изображения
if(cot_module_active('files')){
    $files = files_model_File::findByCondition(array(
        array('file_source', 'advboard'),
    ));
    if(!empty($files)){
        foreach($files as $fileRow){
            $fileRow->delete();
        }
    }
}

// Удалить все комментарии к этому отзыву
if(cot_plugin_active('comments')) {
    if(empty($db_com)) require_once cot_incfile('comments', 'plug');
    cot::$db->delete($db_com, "com_area='advboard'");
}
