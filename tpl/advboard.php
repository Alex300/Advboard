<?php
/**
 * Ads board module for Cotonti Siena
 *    Main template
 *
 * @package Advboard
 * @author Kalnov Alexey <kalnovalexey@yandex.ru>
 * @copyright Portal30 Studio http://portal30.ru
 *
 * @var View $this
 */
?>
<div id="breadcrumbs"><?=$this->breadcrumbs;?></div>

<h1><?=htmlspecialchars($this->page_title)?></h1>

<?php
// Error and message handling
echo $this->displayMessages();

// Shop Cart
// Currency Selector

// Root categories list
if(!empty($this->categories)) {
    foreach($this->categories as $itemRow) { ?>
         <article class="list-row list-row-category">
            <header>
                <?php if(!empty($itemRow['icon'])) { ?>
                    <span style="float:left; margin-right:10px;"><?=$itemRow['icon']?></span>
                <?php } ?>
                <h3 class="title"><a href="<?=cot_url('advboard', array('c' => $itemRow['code']))?>"><?=htmlspecialchars($itemRow['title'])?> ...</a></h3>
            </header>
            <?php if(!empty($itemRow['desc'])) { ?>
                <div class="text"><?=$itemRow['desc']?></div>
            <?php } ?>
        </article>
    <?php }
}