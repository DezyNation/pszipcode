{extends file='helpers/form/form.tpl'}

{block name='defaultForm'}
    <script>
        //error messages for velovalidation.js
        velovalidation.setErrorLanguage({
            only_alphabet: "{l s='Only alphabets are allowed.' mod='productavailabilitycheckbyzipcode'}",
            alphanumeric: "{l s='Field should be alphanumeric.' mod='productavailabilitycheckbyzipcode'}",
            empty_field: "{l s='Field cannot be empty.' mod='productavailabilitycheckbyzipcode'}",
            number_field: "{l s='You can enter only numbers.' mod='productavailabilitycheckbyzipcode'}",            
            positive_number: "{l s='Number should be greater than 0.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_field: "{l s='Field cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_field: "{l s='Field cannot be less than # character(s).' mod='productavailabilitycheckbyzipcode'}",
            empty_zip: "{l s='Please enter zip code.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_zip: "{l s='Zip cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_zip: "{l s='Zip cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            validate_range: "{l s='Number is not in the valid range. It should be betwen ## and ###' mod='productavailabilitycheckbyzipcode'}",
            specialchar_zip: "{l s='Zip should not have special characters.' mod='productavailabilitycheckbyzipcode'}",
            between_percentage: "{l s='Percentage should be between 0 and 100.' mod='productavailabilitycheckbyzipcode'}",
            script: "{l s='Script tags are not allowed.' mod='productavailabilitycheckbyzipcode'}",
            style: "{l s='Style tags are not allowed.' mod='productavailabilitycheckbyzipcode'}",
            iframe: "{l s='Iframe tags are not allowed.' mod='productavailabilitycheckbyzipcode'}",
            html_tags: "{l s='Field should not contain HTML tags.' mod='productavailabilitycheckbyzipcode'}",
            number_pos: "{l s='You can enter only positive numbers.' mod='productavailabilitycheckbyzipcode'}",
            invalid_separator:"{l s='Invalid comma (#) separated values.' mod='productavailabilitycheckbyzipcode'}",
        });
        var kb_path_fold = "{$kb_admin_link}";
    </script>
    {if $version == 16}
        {$form nofilter} {*Variable contains html content, escape not required*}
    {else}
        {$form nofilter} {*Variable contains html content, escape not required*}  
    {/if}
{/block}


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
* @copyright 2016 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Admin tpl file
*}

