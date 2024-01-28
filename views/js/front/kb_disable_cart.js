/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @category  PrestaShop Module
 * @author    knowband.com <support@knowband.com>
 * @copyright 2016 Knowband
 * @license   see file: LICENSE.txt
 */

/**
 * document.ready is responsible for executing the code when the DOM is ready
 * @date 15-02-2023
 * @author 
 * @commenter Vishal Goyal
 */
$(document).ready(function() { 
    $('.add-to-cart').prop('disabled', true);
    $('<div class="kpz_block"><span class="h6">'+disable_addtoacart_msg+'</span>&nbsp;<span id="zipcode_check_click" class="btn btn-info btn-sm">'+checkhere+'</span></div>').insertAfter($('.add-to-cart').parent());
    $("#zipcode_check_click").click(function() {
    $('html, body').animate({
        scrollTop: $(".location_block").offset().top
    }, 500);
});
});
   



