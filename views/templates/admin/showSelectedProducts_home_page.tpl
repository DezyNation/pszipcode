{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Admin tpl file
*}

<div id="kb_home_mapped_product_holder">
    {if isset($selectedhomeproducts) && !empty($selectedhomeproducts)}
        {foreach $selectedhomeproducts as $productDetails}
            <div class="form-control-static">
                <button type="button" onclick="deleteSelectedhomepageProduct({$productDetails['product_id']|escape:'htmlall':'UTF-8'}, this);" class="delMappedProduct btn btn-default" name="{$productDetails['product_id']|escape:'htmlall':'UTF-8'}"><i class="icon-remove text-danger"></i></button>
                &nbsp;{$productDetails['title']|escape:'htmlall':'UTF-8'} ({l s='ref' mod='productavailabilitycheckbyzipcode'}: {$productDetails['reference']|escape:'htmlall':'UTF-8'})
            </div> 
        {/foreach}
    {/if}

</div>
