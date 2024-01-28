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

$(document).ready(function()
{
    
//    $("#multiple-select").select2({
//        placeholder: select_zone_msg,
//        formatNoMatches: no_match_found,
//        allowClear: true
//    });
//    
//    $("#multiple-select-chosen").chosen();
    
    $("[name='product_availability_check_by_zipcode[selected_file]']").parents('.form-group').hide();
    
    if ($('[id^="product_availability_check_by_zipcode[upload_csv]_on"]').is(':checked') == true) {
        $("[name='product_availability_check_by_zipcode[selected_file]']").parents('.form-group').show();
        $("[name^='product_availability_check_by_zipcode[search_product]']").parents('.form-group').hide();
        $("[name^='product_availability_check_by_zipcode_disabled']").parents('.form-group').hide();
            $("#kb_home_mapped_product_holder").parents('.form-group').hide();
        $("[name^='product_availability_check_by_zipcode[select_zone]']").parents('.form-group').hide();
        $("#sample_link").show();
        $("#redirect_link").show();
        
    } else {
        $("[name='product_availability_check_by_zipcode[selected_file]']").parents('.form-group').hide();
        $("[name^='product_availability_check_by_zipcode[search_product]']").parents('.form-group').show();
        $("[name^='product_availability_check_by_zipcode_disabled']").parents('.form-group').show();
            $("#kb_home_mapped_product_holder").parents('.form-group').show();
        $("[name^='product_availability_check_by_zipcode[select_zone]']").parents('.form-group').show();
        $("#sample_link").hide();
        $("#redirect_link").hide();
    }
    
    
    $("[name='product_availability_check_by_zipcode[upload_csv]']").click(function() {
        if ($(this).val() == '1') {
            $("[name='product_availability_check_by_zipcode[selected_file]']").parents('.form-group').show();
            $("[name^='product_availability_check_by_zipcode[search_product]']").parents('.form-group').hide();
            $("[name^='product_availability_check_by_zipcode_disabled']").parents('.form-group').hide();
            $("#kb_home_mapped_product_holder").parents('.form-group').hide();
            $("[name^='product_availability_check_by_zipcode[select_zone]']").parents('.form-group').hide();
            $("#sample_link").show();
            $("#redirect_link").show();
        } else {
            $("[name='product_availability_check_by_zipcode[selected_file]']").parents('.form-group').hide();
            $("[name^='product_availability_check_by_zipcode[search_product]']").parents('.form-group').show();
            $("[name^='product_availability_check_by_zipcode_disabled']").parents('.form-group').show();
            $("#kb_home_mapped_product_holder").parents('.form-group').show();
            $("[name^='product_availability_check_by_zipcode[select_zone]']").parents('.form-group').show();
            $("#sample_link").hide();
            $("#redirect_link").hide();
        }
    });
    $("[name='product_availability_check_by_zipcode[selected_file]']").parents('.form-group').find('.help-block').after($("#sample_link"));
    $("[name='product_availability_check_by_zipcode[selected_file]']").parents('.form-group').find('.help-block').hide();
    $(".panel-heading").after($('#redirect_link'));
    $('#redirect_link').css('margin-left','65%');
});

