/**
 * Ads board module for Cotonti Siena
 *
 * @package Advboard
 * @author Kalnov Alexey    <kalnovalexey@yandex.ru>
 * @copyright (c) Portal30 Studio http://portal30.ru
 */

offset = offset || 0;
offsetTop = offsetTop || 0;

var compareHeader = $('<tr>').attr({id: 'compare-header2'}).css({display:'none', position: 'fixed', top: offsetTop});

var new_sticky_relocate = function () {
    var window_top = $(window).scrollTop();
    var div_top = $('#compare-table').offset().top;
    var div_width = $('#compare-table').width();

    // position static
    $(window).unbind('scroll', new_sticky_relocate);

    if ((window_top + offset) > div_top) {
        compareHeader.addClass('stick').css({display: 'block'}).width(div_width);
    } else {
        compareHeader.removeClass('stick').css({display:'none'});
    }

    $(window).bind('scroll', new_sticky_relocate);
};

$(function() {
    var origHeader = $('#compare-header');
    compareHeader.html(origHeader.html()).insertAfter(origHeader);
    $(window).bind('scroll', new_sticky_relocate);
    new_sticky_relocate();
});