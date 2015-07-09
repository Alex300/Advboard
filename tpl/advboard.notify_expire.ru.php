<?php
/**
 * Notify user: advboard is expiring soon
 *
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright Portal30 Studio http://portal30.ru
 *
 * You can use templates: advboard.notify_user_moderated.<lang>.<categoty>.php
 */

/** @var advboard_model_Advert $advert */
$advert = $this->advert;
$title = (!empty($advert->title)) ? $advert->title : cot::$L['advboard_no_title'];

if($advert->owner['user_id'] == 0) {
?>
Добрый день!
<?php } else { ?>
    Добрый день, <?=htmlspecialchars($advert->owner['full_name'])?>!
<?php } ?>
<p>
    Вы получили это письмо потому, что <b><?=cot_date('date_full', $advert->expire)?></b> истекает срок публикации Вашего объявления на сайте
    «<a href="<?=cot::$cfg['mainurl']?>" target="_blank"><?=htmlspecialchars(cot::$cfg["maintitle"])?></a>».<br />
    После этого оно будет закрыто и больше не будет видно другим пользователям сайта.
</p>
<p>
    <b>«<a href="<?=$this->advertUrl?>" target="_blank"><?=htmlspecialchars($title)?></a>»</b>
</p>

<?php if($this->advertText != '') { ?>
    <hr />
    <?=$this->advertText?>
    <hr />
<?php } ?>

<p>Просмотреть и продлить Ваше объявление Вы можете по адресу: <a href="<?=$this->advertUrl?>" target="_blank"><?=$this->advertUrl?></a>.</p>
<p>Все Ваши объявления: <a href="<?=$this->myAdvsUrl?>" target="_blank"><?=$this->myAdvsUrl?></a>.</p>
<p>Отвечать на это письмо не нужно.</p>
