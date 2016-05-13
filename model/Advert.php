<?php
defined('COT_CODE') or die('Wrong URL.');

if(cot_plugin_active('comments') && !function_exists('cot_comments_remove')) require_once cot_incfile('comments', 'plug');

if(empty($GLOBALS['db_advboard'])) {
    cot::$db->registerTable('advboard');
    cot_extrafields_register_table('advboard');
}

/**
 * Модель advboard_model_Advert
 *
 * Модель объявления
 *
 * @method static advboard_model_Advert getById($pk);
 * @method static advboard_model_Advert fetchOne($conditions = array(), $order = '')
 * @method static advboard_model_Advert[] find($conditions = array(), $limit = 0, $offset = 0, $order = '');
 *
 * @property int    $id             id
 * @property string $alias          Алияс
 * @property int    $state          Состояние
 *                                      0 - Опубликовано, 1 - на модерации, 2 - Черновик
 * @property string $category       Категория
 * @property double $price          Цена
 * @property string $title          Заголовок
 * @property string $description    Краткое описание
 * @property string $text           Текст
 * @property string $person         Контактное лицо
 * @property string $email          Email для связи, если разместил объявление гость
 * @property regioncity_model_City  $city    Город
 * @property string $city_name      Название города
 * @property string $phone          Телефон
 * @property bool   $sticky         Прилеплено?
 * @property int    $begin          Дата начала публикации
 * @property int    $expire         Дата окончания публикации
 * @property int    $sort           Поле для сортировки
 * @property int    $user           id Владельца
 * @property string $views          Количество просмотров
 * @property string $admin_notified Время последнего уведомления администратора об измененеии объявления
 *
 * @property string $created        Дата создания
 * @property int    $created_by     Кем создано
 * @property string $updated        Дата обновления
 * @property int    $updated_by     Кем обновлено
 *
 * ==== Динамические свойства ====
 * @property array $owner
 * @property string $url
 * @property string $editUrl
 * @property string $deleteUrl
 * @property string $validateUrl
 * @property string $cloneUrl
 * @property string $textCut
 *
 * @property int $expireStatus
 */
class advboard_model_Advert extends Som_Model_ActiveRecord
{
    /**
     * @var Som_Model_Mapper_Abstract
     */
    protected  static $_db = null;
    protected  static $_tbname = '';
    protected  static $_primary_key = 'id';

    public static $fetchColumns = array();
    public static $fetchJoins = array();

    /**
     * @var array Владелец объявления
     */
    protected $_owner;

    /**
     * Изменяя эти константы не забудь внести сотвествующие изменения в языковые файлы
     */
    const PUBLISHED = 0;
    const AWAITING_MODERATION = 1;
    const DRAFT = 2;

    const EXPIRING = 3;
    const EXPIRING_TODAY = 4;
    const EXPIRED = 5;

    /**
     * Static constructor
     * @param string $db Data base connection config name
     */
    public static function __init($db = 'db')
    {
        static::$_tbname = cot::$db->advboard;
        parent::__init($db);
    }

    public function __clone()
    {
        parent::__clone();
        
        $this->_owner = null;

        $this->_data['id'] = null;
        $this->_data['alias'] = '';
        $this->_data['state'] = static::DRAFT;
        $this->_data['sticky'] = 0;
        $this->_data['begin'] = 0;
        $this->_data['expire'] = 0;
        $this->_data['sort'] = 0;
        $this->_data['user'] = cot::$usr['id'];
        $this->_data['views'] = 0;
        $this->_data['admin_notified'] = '1970-01-01 00:00:01';
        $this->_data['created'] = date('Y-m-d H:i:s', cot::$sys['now']);
        $this->_data['created_by'] = cot::$usr['id'];
        $this->_data['updated'] = date('Y-m-d H:i:s', cot::$sys['now']);
        $this->_data['updated_by'] = cot::$usr['id'];
    }

    /**
     * Пользователь - Владелец объявления
     * @return array
     */
    public function getOwner()
    {
        if(is_null($this->_owner)){
            if($this->_data['user'] > 0){
                $this->_owner = cot_user_data($this->_data['user']);
                if(empty($this->_owner)){
                    $this->_owner = array(
                        'user_id' => null,
                        'user_maingrp' => 1,
                        'user_name' => cot::$L['Deleted'],
                        'full_name' => cot::$L['Deleted'],
                        'user_avatar' => '',
                    );
                }else{
                    $this->_owner['full_name'] = cot_user_full_name($this->_owner);
                    $this->_owner['url'] = cot_url('users', 'm=details&id=' . $this->_owner['user_id'].
                        '&u='.htmlspecialchars($this->_owner['user_name']));
                }
            }else{
                $this->_owner = array(
                    'user_id' => 0,
                    'user_maingrp' => 1,
                    'user_name' => cot::$L['Guest'],
                    'full_name' => cot::$L['Guest'],
                    'user_avatar' => '',
                );
            }
        }
        return $this->_owner;
    }

    public function issetOwner(){
        if(is_null($this->_owner)) $this->getOwner();

        return (is_array($this->_owner) && $this->_owner['user_id'] > 0);
    }

    public function getPerson($build = false) {
        $person = '';
        $owner = $this->getOwner();

        if(!empty($this->_data['person'])) {
            $person = $this->_data['person'];

        } elseif(!empty($owner)) {
            $person = $owner['full_name'];
        }

        if(empty($person)) return '';
        if(!$build) return $person;

        if(!$owner['user_id']) return htmlspecialchars($person);

        return cot_rc_link($owner['url'], htmlspecialchars($person));

    }

    public function getCity() {
        $cityName = '';
        $owner = $this->getOwner();

        if(!empty($this->_data['city_name'])) {
            $cityName = $this->_data['city_name'];

        } elseif(!empty($owner) && !empty($owner['user_city_name'])) {
            $cityName = $owner['user_city_name'];
        }

        if(empty($cityName)) return '';

        return $cityName;
    }

    public function issetCity(){
        $cityName = $this->getCity();

        return !empty($cityName);
    }

    public function getPhone() {
        $phone = '';
        $owner = $this->getOwner();

        if(!empty($this->_data['phone'])) {
            $phone = $this->_data['phone'];

        } elseif(!empty($owner) && !empty($owner['user_phone'])) {
            $phone = $owner['user_phone'];
        }

        if(empty($phone)) return '';

        return $phone;
    }

    public function issetPhone(){
        $phone = $this->getCity();

        return !empty($phone);
    }

    /**
     * Email. Если не указан в объявлении, то используем email пользователя
     * @param bool $build
     * @return string|void
     */
    public function getEmail($build = false, $ignoreHide = false) {
        $email = '';
        if(!empty($this->_data['email'])) {
            $email = $this->_data['email'];

        } elseif($this->issetOwner() && ($ignoreHide || !$this->_owner['user_hideemail'])) {
            $email = $this->_owner['user_email'];
        }

        if(empty($email)) return '';
        if(!$build) return $email;

        return cot_build_email($email);
    }

    public function issetEmail($ignoreHide = false){
        $email = $this->getEmail(false, $ignoreHide);

        return !empty($email);
    }

    /**
     * Adv url
     * @param bool $htmlspecialchars_bypass If TRUE, will not convert & to &amp; and so on.
     * @return string
     */
    public function getUrl($htmlspecialchars_bypass = false) {
        $urlParams = array('c' => $this->_data['category']);
        if($this->_data['alias'] != '') {
            $urlParams['al'] = $this->_data['alias'];
        } else {
            $urlParams['id'] = $this->_data['id'];
        }

        return cot_url('advboard', $urlParams, '', $htmlspecialchars_bypass);
    }

    /**
     * Adv edit url
     * @param bool $htmlspecialchars_bypass If TRUE, will not convert & to &amp; and so on.
     * @return string
     */
    public function getEditUrl($htmlspecialchars_bypass = false) {
        $urlParams = array(
            'c' => $this->_data['category'],
            'a' => 'edit',
            'id'=> $this->_data['id']
        );
        return cot_url('advboard', $urlParams, '', $htmlspecialchars_bypass);
    }

    /**
     * Adv clone url
     * @param bool $htmlspecialchars_bypass If TRUE, will not convert & to &amp; and so on.
     * @return string
     */
    public function getCloneUrl($htmlspecialchars_bypass = false) {
        $urlParams = array(
            'c' => $this->_data['category'],
            'a' => 'edit',
            'act' => 'clone',
            'id'=> $this->_data['id']
        );
        return cot_url('advboard', $urlParams, '', $htmlspecialchars_bypass);
    }

    /**
     * Adv delete url
     * @param array $urlParams  Redirect back params
     * @param string $msg_key   Language string key which contains confirmation request text
     * @return string
     */
    public function getDeleteUrl($urlParams = null, $msg_key = '') {
        $b = null;
        if(!empty($urlParams)) {
            $b = base64_encode(serialize($urlParams));
        }

        $delUrlParams = array(
            'a' => 'delete',
            'id'=> $this->_data['id'],
        );

        if(!empty($b)) {
            $delUrlParams['b'] = $b;
        }

        return cot_confirm_url(cot_url('advboard', $delUrlParams), 'advboard', $msg_key);
    }

    /**
     * Adv validate url
     * @param string $msg_key Language string key which contains confirmation request text
     * @return string
     */
    public function getValidateUrl($msg_key = '') {
        $urlParams = array(
            'c' => $this->_data['category'],
            'a' => 'validate',
            'id'=> $this->_data['id']
        );
        return cot_confirm_url(cot_url('advboard', $urlParams), 'advboard', $msg_key);
    }

    public function getText() {
        if($this->_data['text'] == '') return '';

        return cot_parse($this->_data['text']);
    }

    public function getTextCut(){
        global $structure;

        // Текст берется через геттер т.к. должет отработать cot_parse
        $textCut = cot_cut_more($this->text);
        $textlength = 0;
        if($this->_data['category'] != '' && isset(cot::$cfg['advboard']['cat_' . $this->_data['category']])) {
            $textlength = cot::$cfg['advboard']['cat_' . $this->_data['category']]['truncatetext'];
            if ($textlength > 0 && mb_strlen($textCut) > $textlength) {
                $textCut = cot_string_truncate($textCut, $textlength, true, false, cot::$R['advboard_cuttext']);
            }
        }
        return $textCut;
    }

    public function getExpireStatus() {
        if ($this->_data['expire'] > 0) {
            $diff = $this->_data['expire'] - cot::$sys['now'];

                if (cot::$cfg['advboard']['expNotifyPeriod'] > 0){

                    if ($diff < (86400 * cot::$cfg['advboard']['expNotifyPeriod']) && $diff > 0){
                        return advboard_model_Advert::EXPIRING;

                    } elseif ($diff <= 0) {
                        return advboard_model_Advert::EXPIRED;
                    }
                }

        }
        return advboard_model_Advert::PUBLISHED;
    }

    /**
     * Может ли текущий пользователь редактировать объявление
     */
    public function canEdit() {
        // Права на любую категорию доски объявлений
        list($authRead, $authWrite, $isAdmin) = cot_auth('advboard', 'any');
        if(!$authRead || !$authWrite) return false;

        if(cot::$usr['id'] > 0 && cot::$usr['id'] == $this->_data['user']) return true;

        list($authRead, $authWrite, $isAdmin) = cot_auth('advboard', $this->_data['category']);
        if($isAdmin) return true;

        if(!$authRead || !$authWrite) return false;

        // Незареги могут править объявы те которые добавили сами
        if (cot::$usr['id'] == 0){
            if (!empty($_SESSION['advboard']) && in_array($this->_data['id'], $_SESSION['advboard'])){
                return true;
            }
        }

        return false;
    }

    protected function afterInsert() {
        if($this->_data['id'] > 0 && cot_module_active('files')) cot_files_linkFiles('advboard', $this->_data['id']);

        // Обновить структуру
        // Наверное не учитываем состояние объявления, а считаем все.
        $count = advboard_model_Advert::count(array(array('category', $this->_data['category'])));
        static::$_db->update(cot::$db->structure, array('structure_count' => $count),
            "structure_area='advboard' AND structure_code=?", $this->_data['category']);

        cot::$cache && cot::$cache->db->remove('structure', 'system');

        return parent::afterInsert();
    }

    protected function beforeUpdate(){
        $this->_data['updated'] = date('Y-m-d H:i:s', cot::$sys['now']);
        $this->_data['updated_by'] = cot::$usr['id'];

        return parent::beforeUpdate();
    }

    protected function afterUpdate() {
        global $structure;

        if($this->_oldData['state'] == advboard_model_Advert::AWAITING_MODERATION &&
            $this->_data['state'] == advboard_model_Advert::PUBLISHED) {
            // Уведомление пользователю о том, что его объявление прошло модерацию
            $this->notifyUserModerated();
        }

        // Обновить структуру, если она изменлась
        if(!empty($this->_oldData['category'])) {
            $count = advboard_model_Advert::count(array(array('category', $this->_data['category'])));
            static::$_db->update(cot::$db->structure, array('structure_count' => $count),
                "structure_area='advboard' AND structure_code=?", $this->_data['category']);

            if (!empty($structure['advboard'][$this->_oldData['category']])) {
                $count = advboard_model_Advert::count(array(array('category', $this->_oldData['category'])));
                static::$_db->update(cot::$db->structure, array('structure_count' => $count),
                    "structure_area='advboard' AND structure_code = ?", $this->_oldData['category']);
            }
            cot::$cache && cot::$cache->db->remove('structure', 'system');
        }

        return parent::afterUpdate();
    }


//    protected function beforeSave() {
//        return parent::beforeSave();
//    }

    protected function afterSave(){
        global $cot_translit_custom, $cot_translit, $structure;

        // Alias
        if(!empty($this->_data['id']) && !empty($this->_data['title'])) {
            $title = $this->_data['title'];

            if(file_exists(cot_langfile('translit', 'core'))) {
                include cot_langfile('translit', 'core');
                if (is_array($cot_translit_custom)) {
                    $title = strtr($title, $cot_translit_custom);

                } elseif (is_array($cot_translit)) {
                    $title = strtr($title, $cot_translit);
                }
            }
            $title = preg_replace('#[^\p{L}0-9\-_ ]#u', '', $title);
            $title = str_replace(' ', '-', $title);
            $title = str_replace('--', '-', $title);
            $title = mb_strtolower($title);

            $cnt = 0;
            // Эти слова зарезервированы
            if(in_array($title, array('all', 'unvalidated', 'saved_drafts'))) $cnt = 1;
            // Алияс не может совпадать с кодом категории
            if(isset($structure['advboard'][$title])) $cnt = 1;

            if($cnt == 0) {
                $cond = array(
                    array('alias', $title),
                    array('id', $this->_data['id'], '!=')
                );
                $cnt = advboard_model_Advert::count($cond);
            }
            if($cnt > 0) $title = $this->_data['id'].'-'.$title;

            $this->_data['alias'] = $title;

            static::$_db->update(static::$_tbname, array('alias' => $this->_data['alias']), 'id='.$this->_data['id']);
        }

        // Для незарега запомним id страницы для чтого, чтобы он мог ее отредактировать в пределах сесии
        if(cot::$usr['id'] == 0){
            if(empty($_SESSION['advboard']) || !in_array($this->_data['id'], $_SESSION['advboard'])) {
                $_SESSION['advboard'][] = $this->_data['id'];
            }
        }

        // Уведомление администратору
        if(cot::$env['location'] != 'administration') {
            $lastNotify = $this->_data['admin_notified'];
            if (!empty($lastNotify)) {
                $tmp = explode(' ', $lastNotify);
                $lastNotify = $tmp[0];
            } else {
                $lastNotify = '1970-01-01';
            }
            $now = date('Y-m-d', cot::$sys['now']);
            if ($lastNotify != $now && (cot::$cfg['advboard']['notifyAdminNewAdv']) || $this->_data['state'] == static::AWAITING_MODERATION) {
                $this->notifyAdmin();
            }
        }
        // Уведомление пользователю, если объявление прошло модерацию находится в методе afterUpdate



        // Сбросим кеш
//        $memCache = auto_getCacheDrv();
//        if($memCache){
//            $cacheKey = 'newReviews';
//            $cacheRealm = 'reviews';
//            $memCache->remove($cacheKey, $cacheRealm);
//
//            $cacheKey = 'widget_review_last';
//            $memCache->remove($cacheKey, $cacheRealm);
//
//            $cacheKey = 'widget_review_last_model_'.$this->_data['model'];
//            $memCache->remove($cacheKey, $cacheRealm);
//        }

        // Сбросим кеш главной страницы
        if(!empty(cot::$cache) && cot::$cfg['cache_index']) cot::$cache->page->clear('index');

        return parent::afterSave();
    }


//    protected function afterUpdate(){
//        return parent::afterUpdate();
//    }

    protected function beforeDelete(){

        // Удалить все файлы и изображения
        if(cot_module_active('files')){
            $files = files_model_File::find(array(
                array('file_source', 'advboard'),
                array('file_item', $this->_data['id'])
            ));
            if(!empty($files)){
                foreach($files as $fileRow){
                    $fileRow->delete();
                }
            }
        }

        // Удалить все комментарии к этому отзыву
        if(cot_plugin_active('comments')) cot_comments_remove('advboard', $this->_data['id']);

        return parent::beforeDelete();
    }

    protected function afterDelete() {
        // Обновить структуру
        // Наверное не учитываем состояние объявления, а считаем все.
        $count = advboard_model_Advert::count(array(array('category', $this->_data['category'])));
        static::$_db->update(cot::$db->structure, array('structure_count' => $count),
            "structure_area='advboard' AND structure_code = ?", $this->_data['category']);

        // Сбросим кеш структуры и главной страницы
        if(!empty(cot::$cache)) {
            cot::$cache->db->remove('structure', 'system');
            if(cot::$cfg['cache_index']) cot::$cache->page->clear('index');
        }

        return parent::afterDelete();
    }

    /**
     * Уведомление администратору
     */
    public function notifyAdmin() {
        global $db_users, $L;

        $usrUrl = cot_url('users', 'm=details&id='.cot::$usr['id'].'&u='.cot::$usr['name'], '', true);
        if (!cot_url_check($usrUrl)) $usrUrl = COT_ABSOLUTE_URL . $usrUrl;

        $advertUrl = $this->getUrl();
        if (!cot_url_check($advertUrl)) $advertUrl = COT_ABSOLUTE_URL . $advertUrl;

        $tmpL = $L;

        $text = $this->_data['description'];
        if(empty($text)) $text = $this->_data['text'];

        $mailView = new View();
        $mailView->advert = $this;
        $mailView->userUrl = $usrUrl;
        $mailView->advertUrl = $advertUrl;
        $mailView->advertText = $text;
        // Шаблон в зависимости от языка администратора и дефолтный
        //$mailBody = $mailView->render

        // TODO на будущее: можно в конфиг добавить настройку со списком e-mail'ов на которые нужно рассылать уведомления
        $admEmails = cot::$db->query("SELECT user_email, user_lang FROM $db_users WHERE user_maingrp=5")->fetchAll(PDO::FETCH_KEY_PAIR);

        $tmp = trim(cot::$cfg['adminemail']);
        if ($tmp != '' && !array_key_exists($tmp, $admEmails)) {
            $admEmails[$tmp] = cot::$cfg['defaultlang'];
        }

        //$email_subject = $L['advboard_created2'].' - '.cot::$cfg['maintitle'];  - это в админке в настройках заголовков и метатегов
        $mailSubject = $L['advboard_created2'];

        $sended = array();
        foreach ($admEmails as $email => $userLang) {
            if (!in_array($email, $sended)) {
                if (empty($userLang)) $userLang = cot::$cfg['defaultlang'];
                include cot_langfile('main', 'core', cot::$cfg['defaultlang'], $userLang);
                include cot_langfile('advboard', 'module', cot::$cfg['defaultlang'], $userLang);

                $mailBody = $mailView->render('advboard.notify_admin_new.' . $userLang . '.' . $this->_data['category']);
                cot_mail($email, $mailSubject, $mailBody, '', false, null, true);
                $sended[] = $email;
            }
        }

        // Вернем язык на место
        $L = $tmpL;

        $this->_data['admin_notified'] = date('Y-m-d H:i:s', cot::$sys['now']);
        static::$_db->update(static::$_tbname, array('admin_notified' => $this->_data['admin_notified']),
            'id='.$this->_data['id']);
    }

    public function notifyUserModerated() {
        global $db_users, $L;

        $advertUrl = $this->getUrl();
        if (!cot_url_check($advertUrl)) $advertUrl = COT_ABSOLUTE_URL . $advertUrl;

        $tmpL = $L;

        $text = $this->_data['description'];
        if(empty($text)) $text = $this->_data['text'];

        $user = null;
        $userLang = cot::$cfg['defaultlang'];

        if(!$this->issetEmail(true)) return false;

        $this->getOwner();

        if($this->_data['user'] > 0) {
            if(cot::$cfg['defaultlang'] != $this->_owner['user_lang']) {
                $userLang = $this->_owner['user_lang'];
                include cot_langfile('main', 'core', cot::$cfg['defaultlang'], $this->_owner['user_lang']);
                include cot_langfile('advboard', 'module', cot::$cfg['defaultlang'], $this->_owner['user_lang']);
            }
        }

        $mailView = new View();
        $mailView->advert = $this;
        $mailView->user = $this->_owner;
        $mailView->advertUrl = $advertUrl;
        $mailView->advertText = $text;

        $mailSubject = $L['advboard_moderated'];
        $mailBody = $mailView->render('advboard.notify_user_moderated.' . $userLang . '.' . $this->_data['category']);
        cot_mail($this->getEmail(false, true), $mailSubject, $mailBody, '', false, null, true);

        // Вернем язык на место
        $L = $tmpL;

        // Сбросить флаг уведомления
        static::$_db->update(static::$_tbname, array('admin_notified' => '1970-01-01 00:00:01'),
            'id='.$this->_data['id']);
    }

    public static function fieldList(){
        $fields = array (
            'id' =>
                array (
                    'type' => 'int',
                    'description' => 'id',
                    'primary' => true,
                ),
            'alias' =>
                array (
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => cot::$L['Alias'],
                ),
            'state' =>
                array (
                    'type' => 'tinyint',
                    'length' => 1,
                    'default' => 0,
                    'description' => cot::$L['Status'],
                ),
            'category' =>
                array (
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'nullable' => false,
                    'description' => cot::$L['Category'],
                ),
            'title' =>
                array (
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => cot::$L['Title'],
                ),
            'price' =>
                array (
                    'type' => 'decimal(15,2)',
                    'default' => 0,
                    'description' => cot::$L['advboard_price'],
                ),
            'description' =>
                array (
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => cot::$L['advboard_desc'],
                ),
            'text' =>
                array (
                    'type' => 'text',
                    'default' => '',
                    'description' => cot::$L['Text'],
                ),
            'person' =>
                array (
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => cot::$L['advboard_person'],
                ),
            // Email для связи, если разместил объявление гость
            'email' =>
                array (
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => cot::$L['Email'],
                ),
            'city' =>
                array (
                    'type' => 'int',
                    'default' => 0,
                    'description' => cot::$L['advboard_city'],    // id города
                ),
            'city_name' =>
                array(
                    'name' => 'city_name',
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => cot::$L['advboard_city'],
                ),
            'phone' =>
                array (
                    'type' => 'varchar',
                    'length' => '255',
                    'default' => '',
                    'description' => cot::$L['advboard_phone'],
                ),
            'sticky' =>
                array (
                    'type' => 'tinyint',
                    'length' => 1,
                    'default' => 0,
                    'description' => cot::$L['advboard_sticky'],
                ),
            'begin' =>
                array (
                    'type' => 'int',
                    'default' => cot::$sys['now'],
                    'description' => cot::$L['Begin'],
                ),
            'expire' =>
                array (
                    'type' => 'int',
                    'default' => 0,
                    'description' => cot::$L['Expire'],
                ),
            'sort' =>
                array (
                    'type' => 'int',
                    'default' => 0,
                    'description' => cot::$L['advboard_sort_date'],
                ),
            'user' =>
                array (
                    'type' => 'int',
                    'default' => 0,
                    'nullable' => false,
                    'description' => 'id Владельца',
                ),
            'views' =>
                array (
                    'type' => 'mediumint',
                    'length' => 8,
                    'default' => 0,
                    'description' => 'Количество просмотров',
                ),
            'admin_notified' =>
                array (
                    'type' => 'datetime',
                    'default' => '1970-01-01 00:00:01',
                    'description' => 'Дата создания',
                ),
            'created' =>
                array (
                    'type' => 'datetime',
                    'default' => date('Y-m-d H:i:s', cot::$sys['now']),
                    'description' => 'Дата создания',
                ),
            'created_by' =>
                array (
                    'type' => 'int',
                    'default' => cot::$usr['id'],
                    'description' => 'Кем создано',
                ),
            'updated' =>
                array (
                    'type' => 'datetime',
                    'default' => date('Y-m-d H:i:s', cot::$sys['now']),
                    'description' => 'Дата обновления',
                ),
            'updated_by' =>
                array (
                    'type' => 'int',
                    'default' => cot::$usr['id'],
                    'description' => 'Кем обновлено',
                ),
        );

        if(cot_plugin_active('regioncity')) {
            $fields['city'] = array(
                'name' => 'city',
                'type' => 'link',
                'default' => 0,
                'description' => cot::$L['advboard_city'],
                'link' =>
                    array(
                        'model' => 'regioncity_model_City',
                        'relation' => SOM::TO_ONE_NULL,
                        'label' => 'title',
                    ),
            );
        }

        return $fields;
    }
}

advboard_model_Advert::__init();