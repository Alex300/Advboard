<?php
/**
 * Ads list template for widget
 *
 * @package Advert
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright Portal30 Studio http://portal30.ru
 */

/** @var advert_model_Advert[] $advertisement */
$advertisement = $this->advertisement;

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
            </div>
        </article>
    <?php }

    if(!empty($this->pagenav['main'])) { ?>
    <div class="text-right">
        <ul class="pagination"><?=$this->pagenav['prev']?><?=$this->pagenav['main']?><?=$this->pagenav['next']?></ul>
    </div>
<?php }
}