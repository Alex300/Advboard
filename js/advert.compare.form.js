/**
 * Ads board module for Cotonti Siena
 *
 * @package Advert
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */

/**
 * добавление к сравнению ajax
 */
$( document ).on( 'change', 'input[type="checkbox"].advert_compare', function(e) {

    var id = $(this).val();
    var x = $('input[name=x]').val();
    var url = 'index.php?e=advert&m=compare&a=ajxAdd';

    if($(this).attr('checked') != 'checked'){
        url = 'index.php?e=advert&m=compare&a=ajxDelete';
    }

    $.post(url, { ids: id, x: x }, function(data) {
        if(data.error != ''){
            alert(data.error);
        }else{
            compareCnt = data.count;
            if(data.count > 0){
                $('#advert_compare-widget').slideDown();

                $.each(data.removedIds, function( index, value ) {
                    $('#advert_compare-widget #advert_compare-widget-row-'+value).remove();
                    $('input.advert_compare[value="'+ value +'"]').removeAttr('checked');
                });

                $.each(data.added, function( index, value ) {

                    var container = $('#advert_compare-widget #advert_compare-widget-row-tpl').clone(),
                        info = container.children('.compare-widget-row-info'),
                        infoText = info.html(),
                        price = '';

                    container.attr('id', 'advert_compare-widget-row-'+value.id).attr('class', 'compare-widget-row');

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

                    container.appendTo('#advert_compare-widget-rows').slideDown();
                });

                if(compareCnt > 1){
                    $('.add-to-compare').fadeIn();
                }else{
                    $('.add-to-compare').fadeOut();
                }

            }else{
                $('#advert_compare-widget .compare-widget-row').remove();
                $('#advert_compare-widget').slideUp();
                $('.add-to-compare').fadeOut();
                $('input.advert_compare').removeAttr('checked');
            }
        }
        $('.tooltip').remove();

    }, "json").fail(function(){
        var toCompareCount = $('input.compare[type="checkbox"]:checked').length;
        if((toCompareCount > 0 && compareCnt > 0) || toCompareCount > 1){
            $('.add-to-compare').fadeIn();
        }else{
            $('.add-to-compare').fadeOut();
        }
    });
});

$( document ).on( 'click', '.advert_compare-widget-delete', function(e) {
    e.preventDefault();

    var ids = '';

    if( $(this).attr('data-id') == 'all'){
        ids = 'all';
    }else{
        var id = $(this).parents('.compare-widget-row').attr('id');
        id = id.replace('advert_compare-widget-row-', '');
        ids = id;
    }

    var x = $('input[name=x]').val();
    var url = 'index.php?e=advert&m=compare&a=ajxDelete';

    $.post(url, { ids: ids, x: x }, function(data) {
        if(data.error != ''){
            alert(data.error);
        }else{
            compareCnt = data.count;
            if(data.count > 0){
                $('#advert_compare-widget').slideDown();

                $.each(data.removedIds, function( index, value ) {
                    $('#advert_compare-widget #advert_compare-widget-row-'+value).remove();
                    $('input.advert_compare[value="'+ value +'"]').removeAttr('checked');
                });

//                var toCompareCount = $('input.compare[type="checkbox"]:checked').length;
                if(compareCnt > 1){
                    $('.add-to-compare').fadeIn();
                }else{
                    $('.add-to-compare').fadeOut();
                }

            }else{
                $('#advert_compare-widget .compare-widget-row').remove();
                $('#advert_compare-widget').slideUp();
                $('.add-to-compare').fadeOut();
                $('input.advert_compare').removeAttr('checked');
            }
        }
        $('.tooltip').remove();

    }, "json").fail(function(){
        alert('Произошла ошибка во время выполнения. Попробуйте позже.');
    });

});


$(function() {
    $( ".add-compare-form" ).submit(function( e ) {
//        var data = $(this).serialize();
        var toCompareCount = compareCnt + $(this).find('input.compare[type="checkbox"]:checked').length;
        if(toCompareCount < 1){
            e.preventDefault();
            return false;
        }

        //убрать лишние параметры
        $('input.compare[type="checkbox"]:not(:checked)').attr('disabled', 'disabled');
    });
});
