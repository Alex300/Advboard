/**
 * Ads board module for Cotonti Siena
 *
 * @package Advert
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */

offset = offset || 0;
offsetTop = offsetTop || 0;

function new_sticky_relocate() {
    var window_top = $(window).scrollTop();
    var div_top = $('#compare-table').offset().top;
    var div_width = $('#compare-table').width();

    // position static

    if ((window_top + offset) > div_top) {
        $('#compare-header').addClass('stick').css({position: 'fixed', top: offsetTop}).width(div_width);
    } else {
        $('#compare-header').removeClass('stick').css('position', 'static');
    }
}

$(function() {
    $(window).scroll(new_sticky_relocate);
    new_sticky_relocate();
});