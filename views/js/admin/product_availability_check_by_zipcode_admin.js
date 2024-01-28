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
    $(".prestashop-switch").css("margin-bottom", "5px");
    
     $('input[name="product_availability_check_by_zipcode_disabled"]').autocomplete(kb_path_fold, {
        delay: 10,
        minChars: 1,
        autoFill: true,
        max: 20,
        matchContains: true,
        mustMatch: true,
        scroll: false,
        cacheLength: 0,
        // param multipleSeparator:'||' ajoutÃ© Ã  cause de bug dans lib autocomplete
        multipleSeparator: '||',
        formatItem: function (item) {
            return item[1] + ' - ' + item[0];
        },
        extraParams: {
            productIds: function () {
                var selected_pro = $('input[name="kb_home_specific_product_items').val();
                return selected_pro.replace(/\-/g, ',');
            },
            excludeVirtuals: 0,
            exclude_packs: 0
        }
    }).result(function (event, item, formatted) {
        addProductToMappedhomepage(item);
        event.stopPropagation();
    });
    
});

function addProductToMappedhomepage(data) {
    console.log(data);
    if (data == null)
        return false;

    var productId = data[1];
    var productName = data[0];
    var $divAccessories = $('#kb_home_mapped_product_holder');
    var delButtonClass = 'delMappedProduct';

    var current_mapped_pro = $('input[name="kb_home_specific_product_items"]').val();
    console.log(current_mapped_pro);
    if (current_mapped_pro != '') {
        var prod_arr_mapped = current_mapped_pro.split(",");
        if ($.inArray(productId, prod_arr_mapped) != '-1') {
            return false;
        }
    }

    $divAccessories.html($divAccessories.html() + '<div class="form-control-static"><button type="button" onclick="deleteSelectedhomepageProduct(' + productId + ',this);" class="' + delButtonClass + ' btn btn-default" name="' + productId + '"><i class="icon-remove text-danger"></i></button>&nbsp;' + productName + '</div>');

    $('input[name="product_availability_check_by_zipcode_disabled"]').val('');

    if (current_mapped_pro != '') {
        $('input[name="kb_home_specific_product_items"]').val(current_mapped_pro + ',' + productId);
    } else {
        $('input[name="kb_home_specific_product_items"]').val(productId);
    }
}


function deleteSelectedhomepageProduct(productId, current) {
    $('input[name="kb_home_specific_product_items"]').val(removeIdFromCommaString($('input[name="kb_home_specific_product_items"]').val(), productId, ','));
    $('input[name="product_availability_check_by_zipcode_disabled"]').val('');
    $(current).parent().remove();
}

function removeIdFromCommaString(list, value, separator) {
    separator = separator || ",";
    var values = list.split(separator);
    for (var i = 0; i < values.length; i++) {
        if (values[i] == value) {
            values.splice(i, 1);
            return values.join(separator);
        }
    }
    return list;
}
