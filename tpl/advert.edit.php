<?php
/**
 * Adv edit template
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright Portal30 Studio http://portal30.ru
 *
 * @note Поля формы можно выводить и "поштучно". Но мне в цикле оказалось гораздо удобнее
 */

//var_dump_($this->category);

/** @var advert_model_Advert $advert */
$advert = $this->advert;

$labelClass = 'col-xs-12 col-md-3';
$elementClass = 'col-xs-12 col-md-9';

$formElements = $this->formElements;
unset($this->formElements);

// Добавим виджет файлов, если необходимо
if(cot::$usr['auth_upload'] && cot_module_active('files') && cot_auth('files', 'a', 'W')) {
    array_insert($formElements, 'text', array ('files' => array (
            'element' => cot_files_filebox('advert', intval($advert->id), '', 'all'),
            'label' => cot::$L['files_attachments'],
        ))
    );
}
echo $this->breadcrumbs
?>
<div class="row">
    <div class="<?=$labelClass?> hidden-xs"></div>

    <div class="<?=$elementClass?>">
        <h2 class="page" style="margin-top: 0;"><?=$this->page_title?></h2>
        <?php if($advert->id > 0) { ?>
            <p><?=cot::$L['advert_id']?>: #<?=$advert->id?></p>
            <p><?=cot::$L['Status']?>: <strong><?=cot::$L['advert_state_'.$advert->state]?></strong></p>
        <?php
        }
        // Error and message handling
        $this->displayMessages();
        ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <form action="<?=$this->formAction?>" enctype="multipart/form-data" method="post" name="advert-form"
              class="form-horizontal" role="form">
            <?php
            echo $formElements['hidden']['element'];
            foreach($formElements as $fldName => $element) {
                if($fldName == 'hidden') continue;

                $elClass = $elementClass;
                if(empty($element['label'])) $elClass .= ' col-md-offset-3';

                ?>
                <div class="form-group <?=cot_formGroupClass($fldName)?>">
                    <?php if(!empty($element['label'])) { ?>
                    <label class="<?=$labelClass?> control-label">
                        <?=$element['label']?>
                        <?php if(!empty($element['required'])) echo ' *';?>
                        :
                    </label>
                    <?php }
                    if($fldName == 'verify') {
                    ?>
                        <div class="<?=$elClass?>">
                            <div class="row">
                                <div class="col-xs-12 col-sm-4"><?=$element['img']?></div>
                                <div class="col-xs-12 col-sm-4">
                                    <div class="hidden-xs" style="margin-top: 22px"></div>
                                    <?=$element['element']?>
                                </div>
                            </div>
                        </div>
                    <?php } elseif($fldName == 'period') { ?>
                        <div class="<?=$elClass?>">
                            <div class="row">
                                <div class="col-xs-12 col-sm-4"><?=$element['element']?></div>
                                <div class="col-xs-12 col-sm-4"><?=cot::$L['advert_days']?></div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="<?=$elClass?>">
                            <?php
                            echo $element['element'];
                            if(isset($element['hint']) && $element['hint'] != '') { ?>
                                <span class="help-block"><?=$element['hint']?></span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span>
                        <?=cot::$L['Save']?></button>

                    <?php if($advert->id > 0) { ?>
                        <a href="<?=$advert->deleteUrl?>" class="btn btn-danger confirmLink">
                            <span class="glyphicon glyphicon-trash"></span> <?=cot::$L['Delete']?></a>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>
</div>