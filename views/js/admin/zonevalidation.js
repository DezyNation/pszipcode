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
var h = 0;

$(document).ready(function()
{
    $('.configure_zip').click(function(){
        $(".error_message").remove();
        $("textarea[name^=product_availability_check_by_zipcode_success]").removeClass('error_field');
        $("textarea[name^=product_availability_check_by_zipcode_failure]").removeClass('error_field');
        $('textarea[name="product_availability_check_by_zipcode[custom_css]"]').removeClass('error_field');
        //$('textarea[name="product_availability_check_by_zipcode[custom_js]"]').removeClass('error_field');
        
        var error = false;
        
//        /*Knowband validation start*/
//                
//        $("textarea[name^=product_availability_check_by_zipcode_success]").each(function(){
//            var suc_msg_err = velovalidation.checkMandatory($(this), 120);
//            if (suc_msg_err != true)
//            {
//                error = true;
//                $(this).addClass('error_field');
//                $(this).after('<span class="error_message">' + suc_msg_err + '</span>');
//            }
//            
//        });
//        /*Knowband validation end*/
//        
//        /*Knowband validation start*/
//        
//        $("textarea[name^=product_availability_check_by_zipcode_failure]").each(function(){
//            var fail_msg_err = velovalidation.checkMandatory($(this), 120);
//            if (fail_msg_err != true)
//            {
//                error = true;
//                $(this).addClass('error_field');
//                $(this).after('<span class="error_message">' + fail_msg_err + '</span>');
//            }
//            
//        });
//        /*Knowband validation end*/

        /*Knowband validation start*/
        var css_msg_err = velovalidation.checkTags($('textarea[name="product_availability_check_by_zipcode[custom_css]"]'));
        var css_html_err = velovalidation.checkHtmlTags($('textarea[name="product_availability_check_by_zipcode[custom_css]"]'));
        if (css_msg_err != true)
        {
            error = true;
//            jQuery('textarea[name="address"]').val();
            
            $('textarea[name="product_availability_check_by_zipcode[custom_css]"]').addClass('error_field');
            $('textarea[name="product_availability_check_by_zipcode[custom_css]"]').after('<span class="error_message">' + css_msg_err + '</span>');
        } else if (css_html_err != true){
            error = true;
//            jQuery('textarea[name="address"]').val();
            
            $('textarea[name="product_availability_check_by_zipcode[custom_css]"]').addClass('error_field');
            $('textarea[name="product_availability_check_by_zipcode[custom_css]"]').after('<span class="error_message">' + css_html_err + '</span>');
        }
        /*Knowband validation end*/
        /*Knowband validation start*/
        var js_msg_err = velovalidation.checkTags($('textarea[name="product_availability_check_by_zipcode[custom_js]"]'));
        if (js_msg_err != true)
        {
            error = true;
            $('textarea[name="product_availability_check_by_zipcode[custom_js]"]').addClass('error_field');
            $('textarea[name="product_availability_check_by_zipcode[custom_js]"]').after('<span class="error_message">' + js_msg_err + '</span>');
        }
        /*Knowband validation end*/
        if(error){
            return false;
        }
        $('.configure_zip').attr('disabled','disabled');
        $('#configuration_form').submit();
    });
    
    $('.zones_add_btn').click(function(){
        $(".error_message").remove();
        $('input[name="product_availability_check_by_zipcode[zone_name]"]').removeClass('error_field');
        $('textarea[name="product_availability_check_by_zipcode[zip-codes]').removeClass('error_field');
        $('input[name="product_availability_check_by_zipcode[deliver_by]').removeClass('error_field');
       
       var error = false;
       
       /*Knowband validation start*/
        var zone_name_err = velovalidation.checkMandatory($('input[name="product_availability_check_by_zipcode[zone_name]"]'), 100);
        var zone_html_err = velovalidation.checkHtmlTags($('input[name="product_availability_check_by_zipcode[zone_name]"]'));
        if (zone_name_err != true)
        {
            error = true;
            $('input[name="product_availability_check_by_zipcode[zone_name]"]').addClass('error_field');
            $('input[name="product_availability_check_by_zipcode[zone_name]"]').after('<span class="error_message">' + zone_name_err + '</span>');
        } else if(zone_html_err != true){
            error = true;
            $('input[name="product_availability_check_by_zipcode[zone_name]"]').addClass('error_field');
            $('input[name="product_availability_check_by_zipcode[zone_name]"]').after('<span class="error_message">' + zone_html_err + '</span>');
        }
        /*Knowband validation end*/
        /*Knowband validation start*/
        var zip_err = velovalidation.checkMandatory($('textarea[name="product_availability_check_by_zipcode[zip-codes]'), 10000);
        var zip_html_err = velovalidation.checkHtmlTags($('textarea[name="product_availability_check_by_zipcode[zip-codes]'));
        if (zip_err != true)
        {
            error = true;
            $('textarea[name="product_availability_check_by_zipcode[zip-codes]').addClass('error_field');
            $('textarea[name="product_availability_check_by_zipcode[zip-codes]').after('<span class="error_message">' + zip_err + '</span>');
        }else if (zip_html_err != true)
        {
            error = true;
            $('textarea[name="product_availability_check_by_zipcode[zip-codes]').addClass('error_field');
            $('textarea[name="product_availability_check_by_zipcode[zip-codes]').after('<span class="error_message">' + zip_html_err + '</span>');
        } else {
            var zipcode_array = $('textarea[name="product_availability_check_by_zipcode[zip-codes]').val().split(",");
            var return_flag = 0;
            zipcode_array.forEach(function(element, index){
                element = element.trim();
                var re = /^[a-zA-Z 0-9-]{4,10}$/;
                if(return_flag == 0) {
                    if(!re.test(element)) {
                        return_flag = 1;
                        error = true;
                        $('textarea[name="product_availability_check_by_zipcode[zip-codes]').addClass('error_field');
                        $('textarea[name="product_availability_check_by_zipcode[zip-codes]').after('<span class="error_message">' + invalid_zipcode_msg + '</span>');
                    }
                }
            });
        }
        /*Knowband validation end*/
        
        /*Knowband validation start*/
        var del_man_err = velovalidation.checkMandatory($('input[name="product_availability_check_by_zipcode[deliver_by]'));
        var del_err = velovalidation.isNumeric($('input[name="product_availability_check_by_zipcode[deliver_by]'));
        if (del_man_err != true)
        {
            error = true;
            $('input[name="product_availability_check_by_zipcode[deliver_by]').addClass('error_field');
            $('input[name="product_availability_check_by_zipcode[deliver_by]').after('<span class="error_message">' + del_man_err + '</span>');
        } else if (del_err != true)
        {
            error = true;
            $('input[name="product_availability_check_by_zipcode[deliver_by]').addClass('error_field');
            $('input[name="product_availability_check_by_zipcode[deliver_by]').after('<span class="error_message">' + del_err + '</span>');
        } else if ($('input[name="product_availability_check_by_zipcode[deliver_by]').val() <= 0 || $('input[name="product_availability_check_by_zipcode[deliver_by]').val() > 999){
            error = true;
            $('input[name="product_availability_check_by_zipcode[deliver_by]').addClass('error_field');
            $('input[name="product_availability_check_by_zipcode[deliver_by]').after('<span class="error_message">' + max_delivery_limit + '</span>');
        }
        /*Knowband validation end*/
        if(error){
            return false;
        }
        $('.zones_add_btn').attr('disabled','disabled');
        $('#configuration_form').submit();
    });
    
    $('.zones_edit_btn').click(function(){
        $(".error_message").remove();
        $('input[name="product_availability_check_by_zipcode[zone_name]"]').removeClass('error_field');
        
        var error = false;
        /*Knowband validation start*/
        var zone_name_err = velovalidation.checkMandatory($('input[name="product_availability_check_by_zipcode[zone_name]"]'), 100);
        var zone_html_err = velovalidation.checkHtmlTags($('input[name="product_availability_check_by_zipcode[zone_name]"]'));
        if (zone_name_err != true)
        {
            error = true;
            $('input[name="product_availability_check_by_zipcode[zone_name]"]').addClass('error_field');
            $('input[name="product_availability_check_by_zipcode[zone_name]"]').after('<span class="error_message">' + zone_name_err + '</span>');
        } else if(zone_html_err != true) {
            error = true;
            $('input[name="product_availability_check_by_zipcode[zone_name]"]').addClass('error_field');
            $('input[name="product_availability_check_by_zipcode[zone_name]"]').after('<span class="error_message">' + zone_html_err + '</span>');
        }
        /*Knowband validation end*/
        if(error){
            return false;
        }
        $('.zones_edit_btn').attr('disabled','disabled');
        $('#configuration_form').submit();
    });
    
    
    $('.view_zone_add_new').click(function(){
        $(".error_message").remove();
        $('.error_field').removeClass('error_field');
        var error = false;
        /*Knowband validation start*/
        if($("input[name='product_availability_check_by_zipcode[upload_csv]']:checked").val() == 0) {
            
            var zip_err = velovalidation.checkMandatory($('textarea[name="product_availability_check_by_zipcode[zip-codes]'), 10000);
            var zip_html_err = velovalidation.checkHtmlTags($('textarea[name="product_availability_check_by_zipcode[zip-codes]'));
            if (zip_err != true)
            {
                error = true;
                $('textarea[name="product_availability_check_by_zipcode[zip-codes]').addClass('error_field');
                $('textarea[name="product_availability_check_by_zipcode[zip-codes]').after('<span class="error_message">' + zip_err + '</span>');
            }else if (zip_html_err != true)
            {
                error = true;
                $('textarea[name="product_availability_check_by_zipcode[zip-codes]').addClass('error_field');
                $('textarea[name="product_availability_check_by_zipcode[zip-codes]').after('<span class="error_message">' + zip_html_err + '</span>');
            } else {
                var zipcode_array = $('textarea[name="product_availability_check_by_zipcode[zip-codes]').val().split(",");
                var return_flag = 0;
                zipcode_array.forEach(function(element, index){
                    element = element.trim();
                    var re = /^[a-zA-Z 0-9-]{4,10}$/;
                    if(return_flag == 0) {
                        if(!re.test(element)) {
                            return_flag = 1;
                            $('textarea[name="product_availability_check_by_zipcode[zip-codes]').addClass('error_field');
                            $('textarea[name="product_availability_check_by_zipcode[zip-codes]').after('<span class="error_message">' + invalid_zipcode_msg + '</span>');
                        }
                    }
                });
            }
            /*Knowband validation end*/

            /*Knowband validation start*/
            var del_man_err = velovalidation.checkMandatory($('input[name="product_availability_check_by_zipcode[deliver_by]'));
            var del_err = velovalidation.isNumeric($('input[name="product_availability_check_by_zipcode[deliver_by]'));
            if (del_man_err != true)
            {
                error = true;
                $('input[name="product_availability_check_by_zipcode[deliver_by]').addClass('error_field');
                $('input[name="product_availability_check_by_zipcode[deliver_by]').after('<span class="error_message">' + del_man_err + '</span>');
            } else if (del_err != true)
            {
                error = true;
                $('input[name="product_availability_check_by_zipcode[deliver_by]').addClass('error_field');
                $('input[name="product_availability_check_by_zipcode[deliver_by]').after('<span class="error_message">' + del_err + '</span>');
            } else if ($('input[name="product_availability_check_by_zipcode[deliver_by]').val() <= 0 || $('input[name="product_availability_check_by_zipcode[deliver_by]').val() > 999){
                error = true;
                $('input[name="product_availability_check_by_zipcode[deliver_by]').addClass('error_field');
                $('input[name="product_availability_check_by_zipcode[deliver_by]').after('<span class="error_message">' + max_delivery_limit + '</span>');
            }
        
            /*Knowband validation end*/
            
        } else {
            if ($('input[name="product_availability_check_by_zipcode[selected_file]"]') == null || $('input[name="product_availability_check_by_zipcode[selected_file]"]').val().trim() == '' || $('input[name="product_availability_check_by_zipcode[selected_file]"]').val() == 'undefined')
            {
                error = true;
                $('input[name="product_availability_check_by_zipcode[selected_file]"]').addClass('error_field');
                $('#uploadedfile-name').parent('div').after('<span class="error_message">' + select_file_message + '</span>');
            } else if($('input[name="product_availability_check_by_zipcode[selected_file]"]').val().split('.').pop() != 'csv'){
                error = true;
                $('input[name="product_availability_check_by_zipcode[selected_file]"]').addClass('error_field');
                $('#uploadedfile-name').parent('div').after('<span class="error_message">' + csv_file_only + '</span>');
            }
        }
        
        if(error){
            return false;
        }
        $('.view_zone_add_new').attr('disabled','disabled');
        $('#configuration_form').submit();
    });
    

    $('.product_map_add').click(function(){
        $(".error_message").remove();
        if ($('select[name="product_availability_check_by_zipcode[search_product][]"]').closest('ul').length) {
            $('select[name="product_availability_check_by_zipcode[search_product][]"]').closest('ul').removeClass('error_field');
        } else {
            $('select[name="product_availability_check_by_zipcode[search_product][]"]').removeClass('error_field');
        }
        $('.error_field').removeClass('error_field');
        var error = false;
        
        /*Knowband validation start*/
        //var pro = $('#multiple-select').val();
        if($("input[name='product_availability_check_by_zipcode[upload_csv]']:checked").val() == 0) {
//            var pro = $('select[name="product_availability_check_by_zipcode[search_product][]"]').val();
            var pro = $('#kb_home_specific_product_items').val();
            if(!pro){
                error = true;

                if ($('input[name="product_availability_check_by_zipcode_disabled"]').closest('ul').length) {
                       $('input[name="product_availability_check_by_zipcode_disabled"]').addClass('error_field');
                   $('input[name="product_availability_check_by_zipcode_disabled"]').after('<span class="error_message">'+select_product_msg+'</span>');
                } else {
                       $('input[name="product_availability_check_by_zipcode_disabled"]').addClass('error_field');
                    $('input[name="product_availability_check_by_zipcode_disabled"]').after('<span class="error_message">'+select_product_msg+'</span>');
                }
            }
            /*Knowband validation end*/

            /*Knowband validation start*/
            var zone = $('select[name="product_availability_check_by_zipcode[select_zone][]"]').val();
            if(!zone){
                    error = true;
                    $('select[name="product_availability_check_by_zipcode[select_zone][]"]').addClass('error_field');
                    $('select[name="product_availability_check_by_zipcode[select_zone][]"]').after('<span class="error_message">'+ select_zone_msg +'</span>');
            }
        } else {
            if ($('input[name="product_availability_check_by_zipcode[selected_file]"]') == null || $('input[name="product_availability_check_by_zipcode[selected_file]"]').val().trim() == '')
            {
                error = true;
                $('input[name="product_availability_check_by_zipcode[selected_file]"]').addClass('error_field');
                $('#uploadedfile-name').parent('div').after('<span class="error_message">' + select_file_message + '</span>');
            } else if($('input[name="product_availability_check_by_zipcode[selected_file]"]').val().split('.').pop() != 'csv'){
                error = true;
                $('input[name="product_availability_check_by_zipcode[selected_file]"]').addClass('error_field');
                $('#uploadedfile-name').parent('div').after('<span class="error_message">' + csv_file_only + '</span>');
            }
        }
        /*Knowband validation end*/

        if(error){
            return false;
        }
        $('.product_map_add').attr('disabled','disabled');
        $('#configuration_form').submit();
    });
    
    $('.view_zone').click(function(){
        $(".error_message").remove();
        $('.error_field').removeClass('error_field');
        var error = false;
        
        /*Knowband validation start*/
        var del_man_err = velovalidation.checkMandatory($('input[name="product_availability_check_by_zipcode[deliver_by]'));
        var del_err = velovalidation.isNumeric($('input[name="product_availability_check_by_zipcode[deliver_by]'));
        if (del_man_err != true)
        {
            error = true;
            $('input[name="product_availability_check_by_zipcode[deliver_by]').addClass('error_field');
            $('input[name="product_availability_check_by_zipcode[deliver_by]').after('<span class="error_message">' + del_man_err + '</span>');
        }else if (del_err != true)
        {
            error = true;
            $('input[name="product_availability_check_by_zipcode[deliver_by]').addClass('error_field');
            $('input[name="product_availability_check_by_zipcode[deliver_by]').after('<span class="error_message">' + del_err + '</span>');
        } else if ($('input[name="product_availability_check_by_zipcode[deliver_by]').val() <= 0 || $('input[name="product_availability_check_by_zipcode[deliver_by]').val() > 999){
            error = true;
            $('input[name="product_availability_check_by_zipcode[deliver_by]').addClass('error_field');
            $('input[name="product_availability_check_by_zipcode[deliver_by]').after('<span class="error_message">' + max_delivery_limit + '</span>');
        }
        /*Knowband validation end*/
        if(error){
            return false;
        }
        $('.view_zone').attr('disabled','disabled');
        $('#configuration_form').submit();
    });
});
