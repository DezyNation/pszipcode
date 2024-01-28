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
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 */

/**
 * document.ready is responsible for executing the code when the DOM is ready
 * @date 15-02-2023
 * @author 
 * @commenter Vishal Goyal
 */
$(document).ready(function() {
//    var value = 0;
//    setCookie("popup_check", value, 365);

    if(kb_zipcode_getCookie("kb_zipcode_popup_check") == ""){
        $('#kbzipcodepopup-modal-backdropDiv').show();
        $('.kbzipcodepopup_container').show();
    }
    
    $('.close_zipcodepopup_modal').click(function(){
        kb_zipcode_setCookie("kb_zipcode_popup_check",1);
        $('#kbzipcodepopup-modal-backdropDiv').hide();
        $('.kbzipcodepopup_container').hide();
    });
});

/**
 * Funtion responsible for checking whether cookie exist in browser or not
 * @date 15-02-2023
 * @author 
 * @commenter Vishal Goyal
 */
function kb_zipcode_getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

/**
 * Funtion responsible for Seeting the cookie
 * @date 15-02-2023
 * @author 
 * @commenter Vishal Goyal
 */
function kb_zipcode_setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

/**
 * Funtion responsible for converting the data string into corresponding timestamp.
 * @date 15-02-2023
 * @author 
 * @commenter Vishal Goyal
 */
function toTimestamp(strDate){
 var datum = Date.parse(strDate);
 return datum/1000;
}

