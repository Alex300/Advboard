<?php
/**
 * Ads board module for Cotonti Siena
 *     Russian Lang file
 * @package Advert
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
$L['advert_add_new'] = 'Подать объявление';
$L['advert_advert'] = 'Объявление';
$L['advert_ads'] = 'Объявления';
$L['advert_ads_board'] = 'Доска объявлений';
$L['advert_captcha'] = 'Введите код с изображения';
$L['advert_city'] = 'Город';
$L['advert_clone'] = 'Дублировать';
$L['advert_compare'] = 'Сравнить';
$L['advert_compare_add'] = 'Добавить к сравнению';
$L['advert_compare_added'] = 'Добавлено к сравнению';
$L['advert_compare_delete'] = 'Удалить из сравнения';
$L['advert_compare_delete_all'] = 'Удалить все из сравнения';
$L['advert_compare_none'] = 'Нет объявлений для сравнения';
$L['advert_created2'] = 'Добавлено новое объявление';
$L['advert_date_begin'] = 'Дата размещения';
$L['advert_days'] = 'Дней';
$L['advert_delete_all'] = 'Удалить все';
$L['advert_desc'] = 'Краткое описание';
$L['advert_edit_category'] = 'Редактировать категорию';
$L['advert_expire'] = 'Истекает срок публикации';
$L['advert_expire_soon'] = 'Публикация истекает через %1$s';
$L['advert_expire_today'] = 'Публикация истекает сегодня';
$L['advert_expired'] = 'Срок публикации истек';
$L['advert_expire_title'] = "Истечение срока публикации Вашего объявления.";
$L['advert_expire_3'] = $L['advert_expire'];
$L['advert_expire_4'] = $L['advert_expire_today'];
$L['advert_expire_5'] = $L['advert_expired'];
$L['advert_future'] = 'Срок публикации не наступил';
$L['advert_id'] = 'Номер объявления';
$L['advert_leave_empty_to_use'] = 'Оставьте пустым чтобы использовать';
$L['advert_moderated'] = 'Объявление проверено';
$L['advert_moderator_filters'] = 'Фильтры модератора';
$L['advert_my_adv'] = 'Мое объявление';
$L['advert_my_ads'] = 'Мои объявления';
$L['advert_my_page'] = 'Моя страница';
$L['advert_new_comment'] = "Новый ответ на Ваше объявление.";
$L['advert_no_title'] = 'Без названия';
$L['advert_not_found'] = 'Объявление не найдено';
$L['advert_owner'] = 'Разместил';
$L['advert_phone'] = 'Телефон';
$L['advert_period'] = 'Срок публикации';
$L['advert_person'] = 'Контактное лицо';
$L['advert_price'] = 'Цена';
$L['advert_price_hint'] = '0 или пусто - договорная';
$L['advert_published'] = 'Опубликовано';
$L['advert_rss_feed'] = 'Последние объявления - ';
$L['advert_set_period'] = 'Установить срок публикации';
$L['advert_sort_date'] = 'Дата для сортировки';
$L['advert_state'] = 'Статус';
$L['advert_state_0'] = 'Опубликовано';
$L['advert_state_1'] = 'Находится на модерации';
$L['advert_state_2'] = 'Черновик';
$L['advert_sticky'] = 'Срочное';
$L['advert_unvalidated'] = 'Объявление поставлено в очередь на модерацию';
$L['advert_user_ads'] = 'Объявления пользователя';
$L['advert_validated'] = 'Объявление утверждено';


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
$L['advert_created'] = 'Ваше объявлние добавлено на сайт';
$L['advert_updated'] = 'Объявлние отредактировано';
$L['advert_awaiting_moderation'] = 'Ваше объявление отправлено на проверку модератора и после проверки будет доступно
 другим пользователям';
$L['advert_deleted'] = 'Объявление «%1$s» удалено';

$L['advert_err_noemail'] = "Вы должны ввести e-mail";
$L['advert_err_wrongmail'] = "Ошибочный e-mail";

/**
 * Admin Part
 */
$L['advert_by_sort_field'] = 'По полю "Дата для сортировки"';


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