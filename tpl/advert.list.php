<?php
/**
 * Ads list template
 *
 * @package Advert
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright Portal30 Studio http://portal30.ru
 */

/** @var advert_model_Advert[] $advertisement */
$advertisement = $this->advertisement;

echo $this->breadcrumbs;
?>
<h1><?=$this->page_title?></h1>

<?php
// Error and message handling
$this->displayMessages();
?>
<div class="row">
    <div class="col-xs-12 col-sm-8">

        <?php
        // If we have structure extrafield 'text'
        if(!empty($this->category['text']) && $this->pagenav['current'] < 2 && $this->pagenavCategory['current'] < 2) { ?>
            <div class="content"><?=$this->category['text']?></div>
        <?php } elseif(!empty($this->category['desc'])) { ?>
            <div class="content text-justify"><?=$this->category['desc']?></div>
        <?php }

        // Add new adv and edit category buttons
        if(!empty($this->addNewUrl)) { ?>
            <div class="text-right">
                <?php if(cot::$usr['isadmin'] && $this->category['id'] > 0) { ?>
                <a href="<?=cot_url('admin', array('m'=>'structure', 'n'=>'advert', 'id'=>$this->category['id']))?>"
                    class="btn btn-info btn-sm">
                    <span class="glyphicon glyphicon-folder-open"></span> &nbsp;<?=cot::$L['advert_edit_category']?></a></a>
                <?php } ?>

                <a href="<?=$this->addNewUrl?>" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-plus"></span>
                    <?=cot::$L['advert_add_new']?></a>
            </div>
        <?php }

        // Categories list
        if(!empty($this->subCategories)) {
            foreach ($this->subCategories as $item) { ?>
                <article class="list-row">
                    <header>
                        <h3>
                            <?php if (!empty($item['icon'])) { ?>
                                <span class="pull-left marginright10"><img src="<?= $item['icon'] ?>"/></span>
                            <?php } ?>
                            <a href="<?= cot_url('advert', array('c' => $item['code'])) ?>"><?= $item['title'] ?> ...</a>
                        </h3>
                    </header>
                    <?php if (!empty($item['desc'])) { ?>
                        <div class="help-block"><?= $item['desc'] ?></div>
                    <?php } ?>
                </article>
            <?php }

            if (!empty($this->pagenavCategory['main'])) { ?>
                <div class="text-right">
                    <ul class="pagination"><?= $this->pagenavCategory['prev'] ?><?= $this->pagenavCategory['main'] ?><?= $this->pagenavCategory['next'] ?></ul>
                </div>
            <?php }
        }

        // Фильтры для модератора
        if($this->category['count'] > 0 && cot::$usr['isadmin']) { ?>
            <div class="well margintop20">
                <h4 style="margin-top: 0"><?=cot::$L['advert_moderator_filters']?>:</h4>
                <form method="get" action="<?=$this->moderatorFilters['action']?>" class="form-inline">
                    <?=$this->moderatorFilters['hidden']?>

                    <div class="form-group">
                        <label><?=cot::$L['advert_state']?></label>
                        <?=$this->moderatorFilters['state'] ?>
                    </div>

                    <div class="form-group">
                        <label><?=cot::$L['advert_period']?></label>
                        <?=$this->moderatorFilters['period'] ?>
                    </div>

                    <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-filter"></span> <?=cot::$L['Submit']?></button>

                    <a href="<?=$this->moderatorFilters['reset']?>" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span></a>
                </form>
            </div>
        <?php }


        // Список объявлений
        if(!empty($advertisement)) {
            foreach ($advertisement as $advRow) {
                //$created =
                ?>
                <article class="list-row row">
                    <div class="col-xs-12">
                        <?php if(cot_module_active('files') && cot_files_count('advert',$advRow->id,'','images') > 0) {
                            $file = cot_files_get('advert', $advRow->id, '');
                            $thumb = cot_files_thumb($file,195,130,'crop');
                            ?>
                        <div class="pull-left marginright10 marginbottom10">
                            <a href="<?=$advRow->url?>" title="<?=htmlspecialchars($advRow->title)?>" rel="bookmark"
                               class="thumbnail"><img alt="<?=htmlspecialchars($advRow->title)?>" src="<?=$thumb?>" /></a>
                        </div>
                        <?php } ?>
                        <header>
                            <h2 style="margin-top: -3px">
                                <a href="<?=$advRow->url?>" rel="bookmark"><?=htmlspecialchars($advRow->title)?></a>
                            </h2>
                            <div>
                                <?php if($advRow->canEdit() && $advRow->user == cot::$usr['id']) { ?>
                                    <span class="label label-success"><?=cot::$L['advert_my_adv']?></span>
                                <?php } ?>

                                <time datetime="<?=date('Y-m-d\TH:i:s+00:00', $advRow->begin)?>"  class="desc">
                                    <?php if(date('Y', $advRow->begin) == date('Y', cot::$sys['now'])) {
                                        echo cot_date('l, d F, G:i', $advRow->begin);
                                    } else {
                                        echo cot_date('datetime_fulltext', $advRow->begin);
                                    } ?>
                                </time>
                                <?php if($this->allowComments) {
                                    $cnt = cot_comments_count('advert', $advRow->id);
                                    ?>
                                    <span class="desc">
                                        &nbsp; | &nbsp;
                                        <a href="<?=$advRow->url?>#comments">
                                            Комментариев <?php echo ($cnt > 0) ? $cnt : cot::$L['No'];?> &raquo;
                                        </a>
                                    </span>
                                <?php } ?>
                            </div>
                        </header>
                        <?php

                        if($advRow->price > 0) { ?>
                        <div class="text-right red strong">
                            <?=number_format($advRow->price, 0, ',', ' ')?> <s>Р</s>
                        </div>
                        <?php }

                        ?>
                        <div class="margintop10">
                            <?php if($advRow->description != '') {
                                echo $advRow->description;
                            } else {
                                echo $advRow->textCut;
                            ?>
                            <?php } ?>
                        </div>

                        <div class="margintop10 text-right">
                            <?=cot_rc('list_more', array('page_url' => $advRow->url))?>
                        </div>
                        <div class="clearfix"></div>

                        <?php if($advRow->canEdit()) {
                            $expireStatus = $advRow->expireStatus;
                            ?>
                            <div class="row margintop10">
                                <div class="col-xs-12 col-md-5">
                                    <?php if($expireStatus == advert_model_Advert::EXPIRED) { ?>
                                        <span class="label label-warning"><?=cot::$L['advert_expire_'.$expireStatus]?></span>
                                    <?php } elseif($expireStatus > 0) { ?>
                                        <span class="label label-danger"><?=cot::$L['advert_expire_'.$expireStatus]?></span>
                                    <?php } ?>

                                    <?php if($advRow->state > advert_model_Advert::PUBLISHED) { ?>
                                        <span class="label label-default"><?=cot::$L['advert_state_'.$advRow->state]?></span>
                                    <?php } ?>

                                    <span class="italic desc">(<?=cot::$L['Hits']?>:&nbsp;<?=$advRow->views?>)</span>
                                </div>

                                <div class="col-xs-12 col-md-7">
                                    <div class="text-right">
                                        <a href="<?=$advRow->editUrl?>" class="btn btn-xs btn-default">
                                            <span class="glyphicon glyphicon-edit"></span> <?=cot::$L['Edit']?></a>

                                        <?php if(cot::$usr['isadmin']) { ?>
                                            <a href="<?=$advRow->validateUrl?>" class="btn btn-xs btn-default confirmLink">
                                                <?php if($advRow->state == advert_model_Advert::AWAITING_MODERATION)  {?>
                                                    <span class="glyphicon glyphicon-check"></span> <?=cot::$L['Validate']?>
                                                <?php } else { ?>
                                                    <span class="glyphicon glyphicon-time"></span> <?=cot::$L['Putinvalidationqueue']?>
                                                <?php } ?></a>

                                            <a href="<?=$advRow->cloneUrl?>" class="btn btn-xs btn-default">
                                                <span class="glyphicon glyphicon glyphicon-duplicate"></span> <?=cot::$L['advert_clone']?></a>
                                        <?php } ?>

                                        <a href="<?=$advRow->getDeleteUrl($this->pageUrlParams)?>" class="btn btn-xs btn-danger confirmLink">
                                            <span class="glyphicon glyphicon-trash"></span> <?=cot::$L['Delete']?></a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if($this->category['config']['compareOn']) {
                            echo advert_compare_checkbox($advRow);
                        } ?>

                    </div>
                </article>
            <?php }

            if(!empty($this->pagenav['main'])) { ?>
            <div class="text-right">
                <ul class="pagination"><?=$this->pagenav['prev']?><?=$this->pagenav['main']?><?=$this->pagenav['next']?></ul>
            </div>
        <?php }
        }
        ?>
    </div>

    <aside class="col-xs-12 col-sm-4">
        <?php if($this->category['config']['compareOn']) {
            echo advert_controller_Widget::compare();
        } ?>
    </aside>
</div>