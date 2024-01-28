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
* @copyright 2016 knowband
* @license   see file: LICENSE.txt
*}
    <style>
        #_desktop_contact_link, #kb_popup_link_container{
            display: inline-table;
        }
        
        .display_nav1_link{
            display: table-cell;
            padding: 0 5px;
        }
    </style>
    <div id="kb_popup_link_container">
        <div class="popup_link">
            <a class = "zipcode_popup_link" href="javascript:void(0)" rel="nofollow">
                <span class="link-item">
                    <i class="material-icons">location_on</i>{if isset($current_zipcode) && $current_zipcode !=''}{$current_zipcode}{else}{l s='Set Your Zipcode' mod='productavailabilitycheckbyzipcode'}{/if}
                </span>
            </a>
        </div> 
    </div>
    
    <script>
        $( ".zipcode_popup_link" ).click(function() {
            if ($('.kbzipcodepopup_container').length) {
                $('.kbzipcodepopup_container').show();
                $('.kbzipcodepopup-modal-backdrop').show();
            }
        });
    </script>