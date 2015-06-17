<?php
/**
 * Ads compare template
 *
 * @package Advert
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright Portal30 Studio http://portal30.ru
 */


global $cot_extrafields;

/** @var advert_model_Advert[] $advertisement */
$advertisement = $this->advertisement;

echo $this->breadcrumbs;
?>
<h1><?=$this->page_title?></h1>

<?php
// Error and message handling
$this->displayMessages();

if ($this->totalitems == 0) { ?>
<h4 class="margintop20 text-center text-muted"><?=cot::$L['advert_compare_none']?></h4>
<?php

} else {

?>
<table id="compare-table" class="table table-condensed table-hover small">
    <tr <?php if(!empty($advertisement)) echo 'id="compare-header"'?>>
        <td style="width: 200px">

        </td>
        <?php foreach($advertisement as $advRow) { ?>
            <td class="text-center" style="width: 120px">
                <div>
                    <a class="red compare-delete" data-toggle="tooltip" title="<?=cot::$L['advert_compare_delete']?>"
                       href="<?=cot_url('advert', array('m'=>'compare', 'a'=>'delete', 'ids'=>$advRow->id))?>">
                        <span class="glyphicon glyphicon-remove"></span> <?=cot::$L['Delete']?>
                    </a>
                </div>
                <?php if(cot_module_active('files') && cot_files_count('advert',$advRow->id,'','images') > 0) {
                    $file = cot_files_get('advert', $advRow->id, '');
                    $thumb = cot_files_thumb($file,120,90,'crop');
                    ?>
                    <img alt="<?=htmlspecialchars($advRow->title)?>" src="<?=$thumb?>" style="width: 120px"/>
                <?php } else { ?>
                    <div class="no-image" style="width: 120px; height: 90px; line-height: 90px"></div>
                <?php }?>
                <a href="<?=$advRow->url?>"><?=htmlspecialchars($advRow->title)?></a>
            </td>
        <?php }  ?>
        <td></td>

    </tr>

    <?php
    // Цена
    $block  = advert_compare_renderRow($advertisement, 'price');
    if($block != '') echo $block;

    // Краткое описание
    $block  = advert_compare_renderRow($advertisement, 'description');
    if($block != '') echo $block;

    /**
     * Экстраполя
     * Если Вам удобнее - можно поля перечислять и в ручную
     */
    if(!empty($cot_extrafields[cot::$db->advert])) {
        // Extra fields for ads
        foreach ($cot_extrafields[cot::$db->advert] as $exfld) {
            $block  = advert_compare_renderRow($advertisement, $exfld['field_name']);
            if($block != '') echo $block;
        }
    }
    ?>
</table>
    <script>
        <?php
        // There is user fixed top panel width class "navbar navbar-default navbar-fixed-top"
        if(cot::$usr['id'] > 0) { ?>
            var offset = 50;
            var offsetTop = '50px';
        <?php } else { ?>
            var offset = 0;
            var offsetTop = 0;
        <?php } ?>
    </script>
<?php }