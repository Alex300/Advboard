/**
 * Ads board module for Cotonti Siena
 *
 * @package Advboard
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */

var advboardWidgetCompare = {
    'selector': '.advboard_compare-widget',
    'countSelector': '.advboard_compare-widget-count'
};

/**
 * Ajax add to compare
 */
$( document ).on( 'change', 'input[type="checkbox"].advboard_compare', function(e) {

    var id = $(this).val(),
        x = $('input[name=x]').val(),
        url = 'index.php?e=advboard&m=compare&a=ajxAdd',
        me = $(this);

    if($(this).attr('checked') != 'checked'){
        url = 'index.php?e=advboard&m=compare&a=ajxDelete';
    }

    $.post(url, { ids: id, x: x }, function(data) {
        if(data.error != ''){
            alert(data.error);

        } else {
            compareCnt = data.count;
            if(data.count > 0) {
                $(advboardWidgetCompare.selector).slideDown();

                if(data.removedIds.length > 0) {
                    // Что то удалили из списка к сравнению
                    me.trigger("advboard.compareItems.deleted", [data]);
                    $.each(data.removedIds, function (index, value) {
                        $('.advboard_compare-widget-rows').each(function (index, element) {
                            $(element).find('#advboard_compare-widget-row-' + value).remove();
                        });
                        $('input.advboard_compare[value="' + value + '"]').removeAttr('checked');
                    });
                }
                $.each(data.added, function( index, value ) {
                    $('.advboard_compare-widget-rows').each(function (index, widgetElement) {
                        var container = $(widgetElement).find('.compare-widget-row-tpl').clone(),
                            info = container.children('.compare-widget-row-info'),
                            infoText = info.html(),
                            price = '';

                        container.attr('id', 'advboard_compare-widget-row-'+value.id).attr('class', 'compare-widget-row');

                        value.price = value.price || 0;
                        if(value.price > 0) price = value.priceFormatted;

                        value.description = value.description || '';

                        infoText = infoText.replace('{COMPARE_URL}', value.url);
                        infoText = infoText.replace('{COMPARE_TITLE}', value.title);
                        infoText = infoText.replace('{COMPARE_PRICE}', price);
                        infoText = infoText.replace('{COMPARE_DESCRIPTION}', value.description);

                        info.html(infoText);

                        if(value.price > 0) info.children('.compare-widget-row-price').show();
                        if(value.description != '') info.children('.compare-widget-row-description').show();

                        container.appendTo(widgetElement).slideDown();

                    });
                });

                if(compareCnt > 1){
                    $('.add-to-compare').fadeIn();
                }else{
                    $('.add-to-compare').fadeOut();
                }

            } else {
                me.trigger( "advboard.compareItems.deleted", [ data ] );
                $(advboardWidgetCompare.selector + ' .compare-widget-row').remove();
                $(advboardWidgetCompare.selector).slideUp();
                $('.add-to-compare').fadeOut();
                $('input.advboard_compare').removeAttr('checked');
            }

            // Update count output
            $(advboardWidgetCompare.countSelector).html(data.count);
        }
        $('.tooltip').remove();

    }, "json").fail(function(){
        var toCompareCount = $('input.compare[type="checkbox"]:checked').length;
        if((toCompareCount > 0 && compareCnt > 0) || toCompareCount > 1){
            $('.add-to-compare').fadeIn();

        } else {
            $('.add-to-compare').fadeOut();
        }
    });
});

$( document ).on( 'click', '.advboard_compare-widget-delete', function(e) {
    e.preventDefault();

    var ids = '',
        me = $(this);

    if( $(this).attr('data-id') == 'all'){
        ids = 'all';

    } else {
        var id = $(this).parents('.compare-widget-row').attr('id');
        id = id.replace('advboard_compare-widget-row-', '');
        ids = id;
    }

    var x = $('input[name=x]').val();
    var url = 'index.php?e=advboard&m=compare&a=ajxDelete';

    $.post(url, { ids: ids, x: x }, function(data) {
        if(data.error != '') {
            alert(data.error);

        } else {
            compareCnt = data.count;

            me.trigger( "advboard.compareItems.deleted", [ data ] );
            if(data.count > 0) {
                $(advboardWidgetCompare.selector).slideDown();

                $.each(data.removedIds, function( index, value ) {
                    $('.advboard_compare-widget-rows').each(function (index, element) {
                        $(element).find('#advboard_compare-widget-row-' + value).remove();
                    });
                    $('input.advboard_compare[value="'+ value +'"]').removeAttr('checked');
                });

//                var toCompareCount = $('input.compare[type="checkbox"]:checked').length;
                if(compareCnt > 1){
                    $('.add-to-compare').fadeIn();

                } else {
                    $('.add-to-compare').fadeOut();
                }

            } else {
                $('.advboard_compare-widget-rows').each(function (index, element) {
                    $(element).find('.compare-widget-row').remove();
                });
                $(advboardWidgetCompare.selector).slideUp();
                $('.add-to-compare').fadeOut();
                $('input.advboard_compare').removeAttr('checked');
            }

            // Update counter output
            $(advboardWidgetCompare.countSelector).html(data.count);
        }
        $('.tooltip').remove();

    }, "json").fail(function() {
        alert('Произошла ошибка во время выполнения. Попробуйте позже.');
    });
});


$(function() {
    $( ".add-compare-form" ).submit(function( e ) {
        var toCompareCount = compareCnt + $(this).find('input.compare[type="checkbox"]:checked').length;
        if(toCompareCount < 1){
            e.preventDefault();
            return false;
        }

        // убрать лишние параметры
        $('input.compare[type="checkbox"]:not(:checked)').attr('disabled', 'disabled');
    });
});
