<?php
/**
 * Ads compare widget template
 *
 * @package Advboard
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright Portal30 Studio http://portal30.ru
 */
?>
<script>
    var compareCnt = <?=$this->totalItems?>;
</script>
<input type="hidden" name="x" value="<?=cot::$sys['xk']?>" />

<div class="advboard_compare-widget panel panel-default" style="margin-top: 0; margin-bottom: 20px;<?php
                if($this->totalItems == 0) { echo " display:none"; }?>">
    <header class="panel-heading">
        <h3 class="panel-title">
            <a href="<?=cot_url('advboard', 'm=compare')?>"><span class="glyphicon glyphicon-tasks"></span>
                <?=cot::$L['advboard_compare_added']?></a>
        </h3>
    </header>
    <div class="panel-body" style="padding:5px 10px 10px 10px;">
        <table class="table table-condensed table-compare advboard_compare-widget-rows">
            <tr class="compare-widget-row-tpl advboard_compare-widget-row-tpl" style="display: none">
                <td class="text-primary width5"><span class="glyphicon glyphicon-chevron-right"></span></td>
                <td class="compare-widget-row-info">
                    <a href="{COMPARE_URL}">{COMPARE_TITLE}</a>
                    <div class="desc compare-widget-row-description" style="display: none; margin: 0">{COMPARE_DESCRIPTION}</div>
                    <div class="compare-widget-row-price" style="display: none"><?=cot::$L['advboard_price']?>: <span class="red">{COMPARE_PRICE}</span></div>
                </td>
                <td class="width5"><a href="#" class="red advboard_compare-widget-delete" title="<?=cot::$L['advboard_compare_delete']?>"
                                      data-toggle="tooltip"><span class="glyphicon glyphicon-remove"></span></a></td>
            </tr>
            <?php

            // Список объявлений
            if(!empty($this->advertisement)) {
                foreach ($this->advertisement as $advRow) {

                    ?>
                <tr id="advboard_compare-widget-row-<?=$advRow['id']?>" class="compare-widget-row">
                    <td class="text-primary width5"><span class="glyphicon glyphicon-chevron-right"></span></td>
                    <td>
                        <a href="<?=$advRow['url']?>"><?=htmlspecialchars($advRow['title'])?></a>
                        <?php if($advRow['description']) { ?>
                            <div class="desc" style="margin: 0"><?=htmlspecialchars($advRow['description'])?></div>
                        <?php }

                        if(!empty($advRow['price'])) { ?>
                            <div><?=cot::$L['advboard_price']?>: <span class="red"><?=$advRow['priceFormatted']?></span></div>
                        <?php } ?>
                    </td>
                    <td class="width5"><a href="#" class="red advboard_compare-widget-delete" title="<?=cot::$L['advboard_compare_delete']?>"
                                          data-toggle="tooltip"><span class="glyphicon glyphicon-remove"></span></a></td>
                </tr>
                <?php }
            } ?>
        </table>
        <div class="margintop10 row">
            <div class="col-xs-6">
                <a href="<?=cot_url('advboard', 'm=compare')?>" class="btn btn-info btn-xs"><?=cot::$L['advboard_compare']?></a>
            </div>

            <div class="col-xs-6 text-right">
                <a href="#" class="advboard_compare-widget-delete" data-id="all" title="<?=cot::$L['advboard_compare_delete_all']?>"
                   data-toggle="tooltip"><?=cot::$L['advboard_delete_all']?></a>
            </div>
        </div>
    </div>
</div>