<?php
/**
 * Notify admin: new advert - email template
 *
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright Portal30 Studio http://portal30.ru
 *
 * You can use templates: advert.notify_admin_new.<lang>.<categoty>.php
 */

/** @var advert_model_Advert $advert */
$advert = $this->advert;
$title = (!empty($advert->title)) ? $advert->title : cot::$L['advert_no_title'];
?>
Добрый день!

<p>
    Пользователь <a href="<?=$this->userUrl?>"><?=cot_user_full_name(cot::$usr['profile'])?></a>
    (<a href="<?=$this->userUrl?>"><?=$this->userUrl?></a>),
    <br /><br />
    добавил или отредактировал объявление: <a href="<?=$this->advertUrl?>"><?=htmlspecialchars($title)?></a>.
</p>
<p>
    Статус объявления: <b><?=cot::$L['advert_state_'.$advert->state]?></b>
</p>
<?php if($this->advertText != '') { ?>
    <hr />
    <?=$this->advertText?>
    <hr />
<?php } ?>
<p>
    Объявление находится по адресу: <a href="<?=$this->advertUrl?>"><?=$this->advertUrl?></a>
</p>