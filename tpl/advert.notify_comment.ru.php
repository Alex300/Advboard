<?php
/**
 * Notify user: advert is expiring soon
 *
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright Portal30 Studio http://portal30.ru
 *
 * You can use templates: advert.notify_user_moderated.<lang>.<categoty>.php
 */

/** @var advert_model_Advert $advert */
$advert = $this->advert;
$title = (!empty($advert->title)) ? $advert->title : cot::$L['advert_no_title'];

if($advert->owner['user_id'] == 0) {
?>
Добрый день!
<?php } else { ?>
    Добрый день, <?=htmlspecialchars($advert->owner['full_name'])?>!
<?php } ?>

<p>Пользователь
    <?php if(!empty($this->commenterUrl)) { ?>
        <a href="<?=$this->commenterUrl?>" target="_blank"><?=htmlspecialchars($this->commenterName)?></a>
    <?php } else {
        echo htmlspecialchars($this->commenterName);
    } ?>
    ответил на Ваше объявление на сайте «<a href="<?=cot::$cfg['mainurl']?>" target="_blank"><?=htmlspecialchars(cot::$cfg["maintitle"])?></a>».
</p>
<?php if(!empty($this->commenter) && !empty($this->commenter['user_email']) && !$this->commenter['user_hideemail']) { ?>
<p>
   Email пользователя: <a href="mailto:<?=$this->commenter['user_email']?>"><?=$this->commenter['user_email']?></a>
</p>
<?php } ?>
<p>
    <b>«<a href="<?=$this->advertUrl?>" target="_blank"><?=htmlspecialchars($title)?></a>»</b>
</p>

<div>
    Ответ на объявление:
    <hr /><?=$this->commentText?><hr />
</div>

<p>
    Просмотреть ответ на Ваше объявление Вы можете по адресу: <a href="<?=$this->commentUrl?>" target="_blank"><?=$this->commentUrl?></a>.
</p>
<p>Все Ваши объявления: <a href="<?=$this->myAdvsUrl?>" target="_blank"><?=$this->myAdvsUrl?></a>.</p>

<p>Отвечать на это письмо не нужно.</p>