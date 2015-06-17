<?php
/**
 * Adv template
 *
 * @package Advert
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright Portal30 Studio http://portal30.ru
 */

/** @var advert_model_Advert $advert */
$advert = $this->advert;

echo $this->breadcrumbs;
?>
<div class="row">
    <div class="col-xs-12 col-sm-8">
        <article>
            <header class="marginbottom10">
                <h1><?=htmlspecialchars($this->page_title)?></h1>
                <?php

                if($advert->canEdit()) {
                    if($advert->user == cot::$usr['id']) { ?>
                        <span class="label label-success"><?=cot::$L['advert_my_adv']?></span>
                    <?php }

                    if ($advert->state != advert_model_Advert::PUBLISHED) { ?>
                        <span class="label label-default"><?=cot::$L['advert_state_'.$advert->state]?></span>
                    <?php }
                }

                if(!empty($advert->begin)) { ?>
                <time datetime="<?=date('Y-m-d\TH:i:s+00:00', $advert->begin)?>" class="desc">
                    <?php if(date('Y', $advert->begin) == date('Y', cot::$sys['now'])) {
                        echo cot_date('l, d F, G:i', $advert->begin);
                    } else {
                        echo cot_date('datetime_fulltext', $advert->begin);
                    } ?>
                </time>
                <?php } ?>
            </header>

            <?php
            // Error and message handling
            $this->displayMessages(false);

            ?>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <?php if($this->category['config']['compareOn']) {
                        echo advert_compare_checkbox($advert);
                    } ?>
                </div>
                <?php

                if($advert->price > 0) { ?>
                <div class="col-xs-12 col-sm-6 text-right">
                    <?=cot::$L['advert_price']?>: <b class="red"><?=$advert->price?></b>
                </div>
                <?php } ?>
            </div>

            <div class="margintop10 marginbottom10">
                <?=$advert->text?>
                <div class="clearfix"></div>
                <?php

                // Виджет файлов (вложений)
                if(cot_module_active('files')) {
                    if (cot_files_count('advert', $advert->id, '', 'images') > 0) { ?>
                        <div class="margintop20"><?= cot_files_gallery('advert', $advert->id) ?></div>
                    <?php }

                    if (cot_files_count('advert', $advert->id, '', 'files') > 0) { ?>
                        ?>
                        <div class="margintop20">
                            <div class="strong"><?=cot::$L['Files']?></div>
                            <?=cot_files_downloads('advert', $advert->id,'')?>
                        </div>
                    <?php }
                } ?>
            </div>
            <div class="clearfix"></div>

            <?php
            // Виджет комментариев
            $commentsLink = cot_comments_link('advert', $this->urlParams, 'advert', $advert->id, $advert->category);
            if(!empty($commentsLink)) {
            ?>
            <footer>
                <div class="text-right margintop20">
                    <?=cot::$L['comments_comments']?>: <?=$commentsLink?>
                </div>
            </footer>
            <?php } ?>
        </article>

        <?php
        // Владелец объявления
        if(!empty($advert->owner)) {
            ?>
            <hr />
            <div class="">
                <span class="text-muted"><?= cot::$L['advert_owner'] ?>:</span>
                <div class="media" style="margin-top: 5px">
                    <div class="media-left">
                        <div class="avatar-sm">
                            <?php if($advert->owner['user_id'] > 0 && cot_module_active('files')) { ?>
                                <a href="<?=$advert->owner['url']?>" class="thumbnail"><?=cot_files_user_avatar($advert->owner['user_avatar'], $advert->owner)?></a>
                            <?php } else { ?>
                                <div class="thumbnail"><?=cot_files_user_avatar($advert->owner['user_avatar'], $advert->owner)?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">
                            <?=$advert->getPerson(true); ?>
                        </h4>

                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <?php if(!empty($advert->city)) { ?>
                                    <div><?=cot::$L['advert_city']?>: <?=$advert->city?></div>
                                <?php }

                                if(!empty($advert->phone)) { ?>
                                    <div><?=cot::$L['advert_phone']?>: <?=$advert->phone?></div>
                                <?php }?>
                            </div>

                            <div class="col-xs-12 col-md-6">
                                <?php if(!empty($advert->email)) { ?>
                                    <div><?=cot::$L['Email']?>: <?=$advert->getEmail(true)?></div>
                                <?php }

                                if($advert->owner['user_id'] > 0 && cot_module_active('pm')) { ?>
                                    <?=cot::$L['users_sendpm']?>: <?=cot_build_pm($advert->owner['user_id'])?>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php
        }

        if($advert->canEdit()) {
            $expireStatus = $advert->expireStatus;
            ?>
        <div class="row margintop20">

            <div class="col-xs-12 col-md-5">
                <?php if($expireStatus == advert_model_Advert::EXPIRED) { ?>
                    <span class="label label-warning"><?=cot::$L['advert_expire_'.$expireStatus]?></span>
                <?php } elseif($expireStatus > 0) { ?>
                    <span class="label label-danger"><?=cot::$L['advert_expire_'.$expireStatus]?></span>
                <?php } ?>

                <?php if($advert->state > advert_model_Advert::PUBLISHED) { ?>
                    <span class="label label-default"><?=cot::$L['advert_state_'.$advert->state]?></span>
                <?php } ?>

                <span class="italic desc">(<?=cot::$L['Hits']?>:&nbsp;<?=$advert->views?>)</span>
            </div>

            <div class="col-xs-12 col-md-7">
                <div class="text-right">
                    <a href="<?=$advert->editUrl?>" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-edit"></i> <?=cot::$L['Edit']?></a>
                    <?php if(cot::$usr['isadmin']) { ?>
                        <a href="<?=$advert->validateUrl?>" class="btn btn-default btn-sm confirmLink">
                            <?php if($advert->state == advert_model_Advert::AWAITING_MODERATION)  {?>
                                <span class="glyphicon glyphicon-check"></span> <?=cot::$L['Validate']?>
                            <?php } else { ?>
                                <span class="glyphicon glyphicon-time"></span> <?=cot::$L['Putinvalidationqueue']?>
                            <?php } ?></a>

                        <a href="<?=$advert->cloneUrl?>" class="btn btn-default btn-sm">
                            <span class="glyphicon glyphicon glyphicon-duplicate"></span> <?=cot::$L['advert_clone']?></a>

                    <?php } ?>

                    <a href="<?=$advert->deleteUrl?>" class="btn btn-danger btn-sm confirmLink">
                        <span class="glyphicon glyphicon-trash"></span> <?=cot::$L['Delete']?></a>
                </div>
            </div>

        </div>

        <?php }

        if(!empty($commentsLink)) { ?>
            <div class="margintop20"><?=cot_comments_display('advert', $advert->id, $advert->category)?></div>
        <?php }

        /**
         * @todo Тут похожие объявления
         */
         ?>
    </div>


    <aside class="col-xs-12 col-sm-4">
        <?php if($this->category['config']['compareOn']) {
            echo advert_controller_Widget::compare();
        } ?>
    </aside>
</div>
