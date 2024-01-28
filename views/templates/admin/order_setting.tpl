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

<script>
        var invalid_zipcode_msg = "{l s='Please enter valid Zipcode(s).' mod='productavailabilitycheckbyzipcode'}";
        var csv_file_only = "{l s='Only CSV file allowed.' mod='productavailabilitycheckbyzipcode'}";
        var select_file_message = "{l s='Please select CSV file.' mod='productavailabilitycheckbyzipcode'}";
        var select_product_msg = "{l s='Please select product.' mod='productavailabilitycheckbyzipcode'}";
        var select_zone_msg = "{l s='Please select Products.' mod='productavailabilitycheckbyzipcode'}";
        var no_match_found = "{l s='No matches found' mod='productavailabilitycheckbyzipcode'}";
        var max_delivery_limit = "{l s='Field value must be between 0 to 1000.' mod='productavailabilitycheckbyzipcode'}";
    
    {if isset($kb_admin_link)}
    var kb_path_fold = "{$kb_admin_link}";
    {/if}
        //error messages for velovalidation.js
        velovalidation.setErrorLanguage({
            empty_fname: "{l s='Please enter First name.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_fname: "{l s='First name cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_fname: "{l s='First name cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            empty_mname: "{l s='Please enter middle name.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_mname: "{l s='Middle name cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_mname: "{l s='Middle name cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            only_alphabet: "{l s='Only alphabets are allowed.' mod='productavailabilitycheckbyzipcode'}",
            empty_lname: "{l s='Please enter Last name.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_lname: "{l s='Last name cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_lname: "{l s='Last name cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            alphanumeric: "{l s='Field should be alphanumeric.' mod='productavailabilitycheckbyzipcode'}",
            empty_pass: "{l s='Please enter Password.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_pass: "{l s='Password cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_pass: "{l s='Password cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            specialchar_pass: "{l s='Password should contain atleast 1 special character.' mod='productavailabilitycheckbyzipcode'}",
            alphabets_pass: "{l s='Password should contain alphabets.' mod='productavailabilitycheckbyzipcode'}",
            capital_alphabets_pass: "{l s='Password should contain atleast 1 capital letter.' mod='productavailabilitycheckbyzipcode'}",
            small_alphabets_pass: "{l s='Password should contain atleast 1 small letter.' mod='productavailabilitycheckbyzipcode'}",
            digit_pass: "{l s='Password should contain atleast 1 digit.' mod='productavailabilitycheckbyzipcode'}",
            empty_field: "{l s='Field cannot be empty.' mod='productavailabilitycheckbyzipcode'}",
            number_field: "{l s='You can enter only numbers.' mod='productavailabilitycheckbyzipcode'}",            
            positive_number: "{l s='Number should be greater than 0.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_field: "{l s='Field cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_field: "{l s='Field cannot be less than # character(s).' mod='productavailabilitycheckbyzipcode'}",
            empty_email: "{l s='Please enter Email.' mod='productavailabilitycheckbyzipcode'}",
            validate_email: "{l s='Please enter a valid Email.' mod='productavailabilitycheckbyzipcode'}",
            empty_country: "{l s='Please enter country name.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_country: "{l s='Country cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_country: "{l s='Country cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            empty_city: "{l s='Please enter city name.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_city: "{l s='City cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_city: "{l s='City cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            empty_state: "{l s='Please enter state name.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_state: "{l s='State cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_state: "{l s='State cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            empty_proname: "{l s='Please enter product name.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_proname: "{l s='Product cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_proname: "{l s='Product cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            empty_catname: "{l s='Please enter category name.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_catname: "{l s='Category cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_catname: "{l s='Category cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            empty_zip: "{l s='Please enter zip code.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_zip: "{l s='Zip cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_zip: "{l s='Zip cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            empty_username: "{l s='Please enter Username.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_username: "{l s='Username cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_username: "{l s='Username cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            invalid_date: "{l s='Invalid date format.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_sku: "{l s='SKU cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_sku: "{l s='SKU cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            invalid_sku: "{l s='Invalid SKU format.' mod='productavailabilitycheckbyzipcode'}",
            empty_sku: "{l s='Please enter SKU.' mod='productavailabilitycheckbyzipcode'}",
            validate_range: "{l s='Number is not in the valid range. It should be betwen ## and ###' mod='productavailabilitycheckbyzipcode'}",
            empty_address: "{l s='Please enter address.' mod='productavailabilitycheckbyzipcode'}",
            minchar_address: "{l s='Address cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_address: "{l s='Address cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            empty_company: "{l s='Please enter company name.' mod='productavailabilitycheckbyzipcode'}",
            minchar_company: "{l s='Company name cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_company: "{l s='Company name cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            invalid_phone: "{l s='Phone number is invalid.' mod='productavailabilitycheckbyzipcode'}",
            empty_phone: "{l s='Please enter phone number.' mod='productavailabilitycheckbyzipcode'}",
            minchar_phone: "{l s='Phone number cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_phone: "{l s='Phone number cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            empty_brand: "{l s='Please enter brand name.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_brand: "{l s='Brand name cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_brand: "{l s='Brand name cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            empty_shipment: "{l s='Please enter Shimpment.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_shipment: "{l s='Shipment cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            minchar_shipment: "{l s='Shipment cannot be less than # characters.' mod='productavailabilitycheckbyzipcode'}",
            invalid_ip: "{l s='Invalid IP format.' mod='productavailabilitycheckbyzipcode'}",
            invalid_url: "{l s='Invalid URL format.' mod='productavailabilitycheckbyzipcode'}",
            empty_url: "{l s='Please enter URL.' mod='productavailabilitycheckbyzipcode'}",
            valid_amount: "{l s='Field should be numeric.' mod='productavailabilitycheckbyzipcode'}",
            valid_decimal: "{l s='Field can have only upto two decimal values.' mod='productavailabilitycheckbyzipcode'}",
            max_email: "{l s='Email cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            specialchar_zip: "{l s='Zip should not have special characters.' mod='productavailabilitycheckbyzipcode'}",
            specialchar_sku: "{l s='SKU should not have special characters.' mod='productavailabilitycheckbyzipcode'}",
            max_url: "{l s='URL cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            valid_percentage: "{l s='Percentage should be in number.' mod='productavailabilitycheckbyzipcode'}",
            between_percentage: "{l s='Percentage should be between 0 and 100.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_size: "{l s='Size cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            specialchar_size: "{l s='Size should not have special characters.' mod='productavailabilitycheckbyzipcode'}",
            specialchar_upc: "{l s='UPC should not have special characters.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_upc: "{l s='UPC cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            specialchar_ean: "{l s='EAN should not have special characters.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_ean: "{l s='EAN cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            specialchar_bar: "{l s='Barcode should not have special characters.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_bar: "{l s='Barcode cannot be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            positive_amount: "{l s='Field should be positive.' mod='productavailabilitycheckbyzipcode'}",
            maxchar_color: "{l s='Color could not be greater than # characters.' mod='productavailabilitycheckbyzipcode'}",
            invalid_color: "{l s='Color is not valid.' mod='productavailabilitycheckbyzipcode'}",
            specialchar: "{l s='Special characters are not allowed.' mod='productavailabilitycheckbyzipcode'}",
            script: "{l s='Script tags are not allowed.' mod='productavailabilitycheckbyzipcode'}",
            style: "{l s='Style tags are not allowed.' mod='productavailabilitycheckbyzipcode'}",
            iframe: "{l s='Iframe tags are not allowed.' mod='productavailabilitycheckbyzipcode'}",
            not_image: "{l s='Uploaded file is not an image.' mod='productavailabilitycheckbyzipcode'}",
            image_size: "{l s='Uploaded file size must be less than #.' mod='productavailabilitycheckbyzipcode'}",
            html_tags: "{l s='Field should not contain HTML tags.' mod='productavailabilitycheckbyzipcode'}",
            number_pos: "{l s='You can enter only positive numbers.' mod='productavailabilitycheckbyzipcode'}",
            invalid_separator:"{l s='Invalid comma (#) separated values.' mod='productavailabilitycheckbyzipcode'}",
        });

</script>

<style>
    .ac_results {
	text-align: left;
	padding: 0px;
	border: 1px solid black;
	background-color: white;
	overflow: hidden;
	z-index: 99999;
}

.ac_results ul {
	width: 100%;
	list-style-position: outside;
	list-style: none;
	padding: 0;
	margin: 0;
}

.ac_results li {
	margin: 0px;
	padding: 2px 5px;
	cursor: default;
	display: block;
	/* 
	if width will be 100% horizontal scrollbar will apear 
	when scroll mode will be used
	*/
	/*width: 100%;*/
	font: menu;
	font-size: 12px;
	/* 
	it is very important, if line-height not setted or setted 
	in relative units scroll will be broken in firefox
	*/
	line-height: 16px;
	overflow: hidden;
}

/*
.ac_loading {
	background: white url('indicator.gif') right center no-repeat;
}
*/

.ac_odd {
	background-color: #eee;
}

.ac_over {
	background-color: #0A246A;
	color: white;
}
</style>

<div id='sample_link' class='download_sample'>
    <a href= '{$download_path nofilter}'>{l s='Download Sample Document from here' mod='productavailabilitycheckbyzipcode'}</a> {*Variable contains URL, escape not required*}
</div>

{if isset($display_export_link) && $display_export_link eq 1}
    <div id='redirect_link' class='redirect_link'>
        <a target="_blank" href= '{$product_link nofilter}'>{l s='You can get the Product Id from here' mod='productavailabilitycheckbyzipcode'}</a><br> {*Variable contains URL, escape not required*}
        <a target="_blank" href= '{$zone_link nofilter}'>{l s='You can get the Zone Id from here' mod='productavailabilitycheckbyzipcode'}</a> {*Variable contains URL, escape not required*}
    </div>
{/if}