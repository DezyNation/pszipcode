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
$(document).ready(function () {

    /**
     * Code responsible to append the zipcode block after aff to cart button
     * @date 23-01-2023
     * @author 
     * @commenter Vishal Goyal
     */
    $('.product-add-to-cart').after($('#zip_code_block'));

    
    if (typeof disable_addtocart !== 'undefined') {
        if (disable_addtocart == '1') {
            $('.add-to-cart').prop('disabled', true);
            $('<div class="kpz_block"><span class="h6">' + disable_addtoacart_msg + '</span>&nbsp;</div>').insertAfter($('.product-quantity').parent());

        }
    }
    $('.product-add-to-cart').after($('#zip_code_block'));
    //changes made by MK
    $('#zip_code_block').append('<input type="hidden" name="kb_is_valid" value="0">');
    if ($('input[name="type_zipcode"]').length) {
    if ($('#type_zipcocde').val() != undefined) {
        validFrontLocation();
        }
    }
    $('#zip_code_block').show();

    if (typeof product_not_available !== 'undefined') {
        if (product_not_available) {
            $('button[name="confirmDeliveryOption"]').prop('disabled', true);
            $('#checkout-delivery-step').attr('class', 'checkout-step -reachable -current')
            $('#checkout-payment-step').attr('class', 'checkout-step -unreachable');
        }
    }
    //changes made by MK
    $('.product-actions').bind("DOMSubtreeModified", function () {
//        event.stopImmediatePropagation();
        if ($('input[name="kb_is_valid"]').val() === '0') {
            disableInclusive();
        }
    });
    //changes made by MK
    //changes done by tarun gupta for the Add to cart button issue reported by shubham
//    function disableInclusive()
//    {
//        $('.add-to-cart').prop('disabled', true);
//        if (typeof product_not_available !== 'undefined') {
//            if (product_not_available) {
//                $('button[name="confirmDeliveryOption"]').prop('disabled', true);
//                $('.add-to-cart').prop('disabled', true);
//            }
//        }
//    }
    function disableInclusive()
    {
        if (typeof(disable_addtocart) != "undefined" && disable_addtocart == '1') {
            $('.add-to-cart').prop('disabled', true);
        }
        if (typeof product_not_available !== 'undefined') {
            if (product_not_available) {
                $('button[name="confirmDeliveryOption"]').prop('disabled', true);
                if (disable_addtocart == '1') {
                    $('.add-to-cart').prop('disabled', true);
                }
            }
        }
    }
    //changes over
    $(document).ajaxComplete(function (event, xhr, settings) {
        if (settings.data != null) {
        var str = settings.data;
        var str_arr = str.split('&');
        var action_arr = str_arr[0].split('=');
        var action = action_arr[1];
        if (action == 'quickview') {
            if (disable_addtocart == '1') {
                $('.add-to-cart').prop('disabled', true);
                $('<div class="kpz_block"><span class="h6">' + disable_addtoacart_msg + '</span>&nbsp;<span id="zipcode_check_click" class="btn btn-info btn-sm">' + checkhere + '</span></div>').insertAfter($('.add-to-cart').parent());
                $("#zipcode_check_click").click(function () {
                    $('.quickview').animate({
                        scrollTop: $(".quickview .location_block").offset().top
                    }, 500);
                });
            }
        }
    }
    });

    $("#location_check").appendTo($("#usefull_link_block"));
});

/**
 * Event added for zipcode popup, when pressing "Enter"
 * @date 23-01-2023
 * @author 
 * @commenter Vishal Goyal
 */
function onKeyPressZipcode(event) {
    if (event.which == '13') {
        event.preventDefault();
//        validFrontLocation();
    }
}

/**
 * Function responsible for checking whether the products is avaiable in range or Not
 * @date 23-01-2023
 * @author 
 * @commenter Vishal Goyal
 */
function validFrontLocation() {
    $("#message_123").html('');
    var type_zip = document.getElementById("type_zipcode").value;
    var id_product = document.getElementById("kb_id_product").value;
    var error = 0;
    var js_error = false;
    $('input[name="kb_is_valid"]').val(0);   //changes made by MK
    /*Knowband validation start*/
    $('.velsof_zone_err_msg').remove();
    var re = /^[a-zA-Z 0-9-]{4,10}$/;
    var zip_err = velovalidation.checkMandatory($('#type_zipcode'));
    if (zip_err != true)
    {
        js_error = true;
        $("#message_123").html('<p class="alert alert-danger">'+ zip_err +'</p>')
        //$('#type_zipcode').parent('div').after('<span class="velsof_zone_err_msg" style="color: red;">' + zip_err + '</span>');
    } else if(!re.test($('#type_zipcode').val())) {
        js_error = true;
        $("#message_123").html('<p class="alert alert-danger">'+ valid_zip_msg +'</p>')
        //$('#type_zipcode').parent('div').after('<span class="velsof_zone_err_msg" style="color: red;">' + valid_zip_msg + '</span>');
    }
    /*Knowband validation end*/

    if (!js_error) {
        check_ajax_path = check_ajax_path.replace(/&amp;/g, "&");

        $.ajax({
            url: check_ajax_path + ((check_ajax_path.indexOf('?') < 0) ? '?' : '&') + $('#add-to-cart-or-refresh').serialize(),
            type: "POST",
            data: {zip_code_value: type_zip, id_product: id_product, ajax: true},
            dataType: 'json',
            func:'checkzipcode',
            beforeSend: function () {
                $('#check_zipcode').attr('disabled', 'disabled');
            },
            success: function (json) {
                $("#message_123").html(json['html']);
                $('input[name="kb_is_valid"]').val(1);  //changes made by MK
                if (disable_addtocart == '1') {
                    if (json['msg'] === 2) {
                        $('.add-to-cart').prop('disabled', false);
                        $('.kpz_block').hide();
                    } else {
                        $('.add-to-cart').prop('disabled', true);
                        $('.kpz_block').show();
                        $('input[name="kb_is_valid"]').val(0);      //changes made by MK

                    }
                }
            },
            complete: function () {
                $('#check_zipcode').removeAttr('disabled');
                if($('.zipcode_popup_link').length) {
                    $('.zipcode_popup_link').html("<span class='link-item'><i class='material-icons'>location_on</i>"+type_zip+"</span>")
                }
                if($('.kbzipcodepopup_modal_body .kb-block').length) {
                    $('.kbzipcodepopup_modal_body .kb-block h5').html(type_zip)
                }
            }
        });

        if (error) {
            return false;
        } else {
            return true;
        }
    }
}
