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

{if $message_value eq '1'}
    <p class="alert alert-danger">{l s='Please enter the zip-code.' mod='productavailabilitycheckbyzipcode'}</p>
{/if}
{if $message_value eq '2'}
    <p class="alert alert-success">{l s='Product is available at this location.' mod='productavailabilitycheckbyzipcode'} {if $display_delivery eq '1'}<br>
    {l s='Delivery in ' mod='productavailabilitycheckbyzipcode'}&nbsp;{$number_of_days}&nbsp;{l s=' days,' mod='productavailabilitycheckbyzipcode'}
    {$delivery_date}</p>{/if}
{/if}
{if $message_value eq '3'}
    <p class="alert alert-danger">{l s='Product is not available at this location.' mod='productavailabilitycheckbyzipcode'}</p>
{/if}
{if $message_value eq '4'}
    <p class="alert alert-danger">{l s='Please enter a valid zip-code.' mod='productavailabilitycheckbyzipcode'}</p>
{/if}
{if $message_value eq '5'}
    <p class="alert alert-danger">{l s='The Selected Product (attribute) is not available in stock .' mod='productavailabilitycheckbyzipcode'}</p>
{/if}