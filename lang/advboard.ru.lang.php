<?php
/**
 * Ads board module for Cotonti Siena
 *     Russian Lang file
 * 
 * @package Advboard
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL');

/**
 * Module Title & Subtitle
 */
$L['info_name'] = 'Доска объявлений';
$L['info_desc'] = 'Модуль доски объявлений';

/**
 * Module Body
 */
$L['advboard_add_new'] = 'Подать объявление';
$L['advboard_advert'] = 'Объявление';
$L['advboard_ads'] = 'Объявления';
$L['advboard_ads_board'] = 'Доска объявлений';
$L['advboard_captcha'] = 'Введите код с изображения';
$L['advboard_city'] = 'Город';
$L['advboard_clone'] = 'Дублировать';
$L['advboard_compare'] = 'Сравнить';
$L['advboard_compare_add'] = 'Добавить к сравнению';
$L['advboard_compare_added'] = 'Добавлено к сравнению';
$L['advboard_compare_delete'] = 'Удалить из сравнения';
$L['advboard_compare_delete_all'] = 'Удалить все из сравнения';
$L['advboard_compare_none'] = 'Нет объявлений для сравнения';
$L['advboard_created2'] = 'Добавлено новое объявление';
$L['advboard_date_begin'] = 'Дата размещения';
$L['advboard_days'] = 'Дней';
$L['advboard_delete_all'] = 'Удалить все';
$L['advboard_desc'] = 'Краткое описание';
$L['advboard_edit_category'] = 'Редактировать категорию';
$L['advboard_expire'] = 'Истекает срок публикации';
$L['advboard_expire_soon'] = 'Публикация истекает через %1$s';
$L['advboard_expire_today'] = 'Публикация истекает сегодня';
$L['advboard_expired'] = 'Срок публикации истек';
$L['advboard_expire_title'] = "Истечение срока публикации Вашего объявления.";
$L['advboard_expire_3'] = $L['advboard_expire'];
$L['advboard_expire_4'] = $L['advboard_expire_today'];
$L['advboard_expire_5'] = $L['advboard_expired'];
$L['advboard_future'] = 'Срок публикации не наступил';
$L['advboard_id'] = 'Номер объявления';
$L['advboard_leave_empty_to_use'] = 'Оставьте пустым чтобы использовать';
$L['advboard_moderated'] = 'Объявление проверено';
$L['advboard_moderator_filters'] = 'Фильтры модератора';
$L['advboard_my_adv'] = 'Мое объявление';
$L['advboard_my_ads'] = 'Мои объявления';
$L['advboard_my_page'] = 'Моя страница';
$L['advboard_new_comment'] = "Новый ответ на Ваше объявление.";
$L['advboard_no_title'] = 'Без названия';
$L['advboard_not_found'] = 'Объявление не найдено';
$L['advboard_owner'] = 'Разместил';
$L['advboard_phone'] = 'Телефон';
$L['advboard_period'] = 'Срок публикации';
$L['advboard_person'] = 'Контактное лицо';
$L['advboard_price'] = 'Цена';
$L['advboard_price_hint'] = '0 или пусто - договорная';
$L['advboard_published'] = 'Опубликовано';
$L['advboard_rss_feed'] = 'Последние объявления - ';
$L['advboard_set_period'] = 'Установить срок публикации';
$L['advboard_sort_date'] = 'Дата для сортировки';
$L['advboard_state'] = 'Статус';
$L['advboard_state_0'] = 'Опубликовано';
$L['advboard_state_1'] = 'Находится на модерации';
$L['advboard_state_2'] = 'Черновик';
$L['advboard_sticky'] = 'Срочное';
$L['advboard_unvalidated'] = 'Объявление поставлено в очередь на модерацию';
$L['advboard_user_ads'] = 'Объявления пользователя';
$L['advboard_validated'] = 'Объявление утверждено';


$Ls['advert_advertisement'] = "объявление,объявления,объявлений";

// === old ===
$L['advboard']['your_email'] = 'Ваш e-mail';
$L['advboard']['page_created_log'] = 'Добавлено новое объявление';

$L['advboard']['edit_page'] = 'Редактировать объявление';
$L['advboard']['from_now'] = 'С сегодняшнего дня.';
$L['advboard']['anonimus'] = "Анонимный";

$L['advboard']['avd_count'] = 'Количество объявлений';
$L['advboard']['read_more'] = 'Подробнее';
$L['advboard']['recent_advs'] = 'Последние объявления';
$L['advboard']['read_more'] = 'Читать полностью';
// === /old ===

//$L['an_adv_board']['wait_validation'] = 'ожидает модерации';
//$L['an_adv_board']['hits'] = 'Просмотров';

/**
 * Errors and messages
 */
$L['advboard_created'] = 'Ваше объявлние добавлено на сайт';
$L['advboard_updated'] = 'Объявлние отредактировано';
$L['advboard_awaiting_moderation'] = 'Ваше объявление отправлено на проверку модератора и после проверки будет доступно
 другим пользователям';
$L['advboard_deleted'] = 'Объявление «%1$s» удалено';

$L['advboard_err_noemail'] = "Вы должны ввести e-mail";
$L['advboard_err_wrongmail'] = "Ошибочный e-mail";

/**
 * Admin Part
 */
$L['advboard_by_sort_field'] = 'По полю "Дата для сортировки"';


/**
 * Module Config
 */
$L['cfg_firstCrumb'] = 'Выводить ссылку на главную страницу доски в хлебных крошках';
$L['cfg_periodOrder'] = 'Порядок заполнения &laquo;Период&raquo;';
$L['cfg_periodOrder_hint'] = 'При подаче объявления выпадающий список &laquo;Период&raquo; заполнен';
$L['cfg_periodOrder_params'] = array(
    'desc' => 'По убыванию',
    'asc' => 'По возрастанию'
);
$L['cfg_notifyAdminNewAdv'] = array('Уведомлять администратора о новых объявлениях?');
$L['cfg_notifyUserNewComment'] = array('Уведомлять пользователя о новых комментариях на его объявления?');
//$L['cfg_notifyUserAdvExpire'] = array('Уведомлять пользователя об истечении срока публикации объявления?');
$L['cfg_expNotifyPeriod'] = array("Уведомлять пользователя об истечении срока публикации объявления за", 'дней. 0 - не уведомлять.');
$L['cfg_guestEmailRequire'] = array('Гостям обязательно указывать e-mail при подаче объявления?');
$L['cfg_rssToHeader'] = array('Вывести ссылку на RSS ленту &laquo;Последних объявлений&raquo; в header.tpl?');

$L['cfg_guestUseCaptcha'] = array('Использовать капчу для подачи объявлений гостями?', "Будет использована только для
    незарегистрированных пользователей.<br />Капча должна быть установлена на сайте.");



$L['cfg_allowSticky'] = 'Разрешить срочные объявления?';
$L['cfg_allowSticky_hint'] = 'Те объявления, у которых отмечено поле, указанное ниже, отображаются в первую очередь';
$L['cfg_compareOn']   = 'Разрешить сравнение объявлений?';
$L['cfg_compareOn_hint'] = 'Позволяет вывести сравнительную таблицу выбранных объявлений';
$L['cfg_title_require'] = 'Заголовок обязательно заполнять?';
$L['cfg_city_require'] = 'Город обязательно заполнять?';
$L['cfg_phone_require'] = 'Телефон обязательно заполнять?';
$L['cfg_maxPeriod'] = 'Максимальный срок размещения объявления';
$L['cfg_maxPeriod_hint'] = 'Срок в днях. Например: 30. 0 - неограничено';