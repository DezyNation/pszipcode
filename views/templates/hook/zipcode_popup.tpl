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
* Front tpl file
*}

{*changes done by tarun for the custom css and js issue for popup*}
<style>
    {if isset($custom_css)}
        {$custom_css nofilter} {*Variable contains css content, escape not required*}
    {/if}
</style>
<script>
    {if isset($custom_js)}
            document.addEventListener("DOMContentLoaded", function(event) {
        {$custom_js nofilter} {*Variable contains css content, escape not required*}
            });
    {/if}
</script>
{*changes over*}
<script>
    var axc_path_zipcode = "{$zipcodepage nofilter}"; {* Variable contains HTML/CSS/JSON, escape not required *}
    {if isset($current_zipcode)}
        var current_zipcode = "{$current_zipcode|escape:'htmlall':'UTF-8'}";
    {/if}
</script>
{literal}
    <script>
        function kb_zipcodetpl_setCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 60 * 1000));
            var expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }
        function setZipcodeEntered(zipcode) {
            $.ajax({
                type: "POST",
                url: axc_path_zipcode,
                data: 'ajax=1&method=setpopupzipcode&zipcode='+zipcode,
                success: function(q) {
                    var data = jQuery.parseJSON(q);
                    if (data.status == true) {
                        kb_zipcodetpl_setCookie("kb_zipcode_popup_check",1);
                        $('#kbzipcodepopup-modal-backdropDiv').hide();
                        $('.kbzipcodepopup_container').hide();
                        location.reload(true);
                    } else {
                        return false;
                    }
                }
            });
        }
    </script>
{/literal}   
<div id="kb_zipcodepopup_container" style="">
    <div id="kbzipcodepopup-modal-backdropDiv" class="kbzipcodepopup-modal-backdrop" style="display:none;"></div>
    <div class="kbzipcodepopup_container" style="display:none;">
        <div class="kbzipcodepopup_modal animated zoomInUp" >
            <div class="kbzipcodepopup_modal_content_section">
                <div class="close_zipcodepopup_modal">X</div>                   
                <div class="kbzipcodepopup_modal_body" style="color:{$kb_color_data}">
                    {if isset($current_zipcode) && $current_zipcode !=''}
                        <div class="kb-block" style="padding:5px 15px 5px 5px; text-align: Left">
                            <h1 style="text-align: Left;"><strong>{l s='My Zipcode : ' mod='productavailabilitycheckbyzipcode'}</strong></h1>
                            <h5 style="">{$current_zipcode}</h5>
                        </div>
                        <h1 style="text-align: Left;"><strong>{l s='Change Your Zipcode:' mod='productavailabilitycheckbyzipcode'}</strong></h1>
                    {else}
                        <h1 style="text-align: Left;"><strong>{l s='Enter Your Zipcode:' mod='productavailabilitycheckbyzipcode'}</strong></h1>
                    {/if}
                    <form action="" id="zipcodepopupForm" method="post" class="" enctype="multipart/form-data">
                    <ul class="kb-form-list">
                            <li class="kb-form-fwidth">
                                <div class="kb-form-field-block">
                                    <input type="text" name="popupzipcode" id="popupzipcode" placeholder="{l s='Enter Your Zipcode' mod='productavailabilitycheckbyzipcode'}" value='' class="autocomplete kb-inpfield pac-target-input" required="required" autocomplete="off" style="width: -webkit-fill-available;background-image: none;">
                                </div>
                            </li>
                    </ul>
                    <div class="velo-search-button velo-field-inline" style="margin-top: 2%; text-align: left;">
                        <button type="button" id="zipcodeadd" class="kbbtn-big kbbtn-success">{l s='Confirm' mod='productavailabilitycheckbyzipcode'}</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
{*<script src="{$kbsrc}/views/js/front/jquery-1.11.0.min.js"></script>*}
<script type="text/javascript" src="{$kbsrc}/views/js/front/zipcodepopup.js" ></script>

<script>
$( "#close_location" ).click(function() {
  $('.kb-cs-notify-overlay').hide();
  $('.kb-cs-notify-block').hide();
});
$(document).on("click", "#zipcodeadd", function () {
    var postcode_entered = $('#popupzipcode').val();
    if (postcode_entered != ''){
        setZipcodeEntered(postcode_entered);
    }else{
        alert('please enter your zipcode');
    }
});
</script>                            

