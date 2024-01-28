<?php
/**
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
* @category  PrestaShop Module
*
*
* Description
*
* Updates quantity in the cart
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_.'productavailabilitycheckbyzipcode/classes/ViewZone.php');
class AdminViewZoneController extends ModuleAdminControllerCore
{
    /**
     * Function responsible to Creating list of all available zipcode for specific zones
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function __construct()
    {
        $this->context = Context::getContext();
        $zone_id  = Tools::getValue('id_kb_pacbz_zones');
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Saving cookie for ZONE ID
        if ($zone_id != null) {
            $this->context->cookie->__set('zone_cookie2', $zone_id);
            $this->context->cookie->viewzonekb_pacbz_zone_zipcode_mappingFilter_zipcode = '';
            $this->context->cookie->viewzonekb_pacbz_zone_zipcode_mappingFilter_id_kb_pacbz_zone_zipcode_mapping = '';
            $this->context->cookie->viewzonekb_pacbz_zone_zipcode_mappingFilter_deliver_by = '';
            $this->context->cookie->viewzonekb_pacbz_zone_zipcode_mappingFilter_availability = '';
        } else {
            $zone_id = $this->context->cookie->zone_cookie2;
        }
        if (($zone_id === false) || (Tools::isEmpty($zone_id))) {
            $linkobj = new Link();
            $red_path = $linkobj->getAdminLink('AdminGlobalZones');
            Tools::redirectAdmin($red_path);
        }
        $sql = 'SELECT zone_name FROM `'
            ._DB_PREFIX_.'kb_pacbz_zones` where id_kb_pacbz_zones ="' .pSQL($zone_id).'"';
        $zone_name = Db::getInstance()->ExecuteS($sql);

        $this->page_header_toolbar_title = $zone_name[0]['zone_name'];
        $this->table = 'kb_pacbz_zone_zipcode_mapping';
        $this->className = 'ViewZone';
        
        $this->show_toolbar = true;
        $this->list_no_link = true;
        $this->bootstrap = true;
        $this->_select = '`id_kb_pacbz_zone_zipcode_mapping`,`zipcode`,`deliver_by`,`availability`';
        $this->_where = 'AND zone_id ="' .pSQL($zone_id).'"';
        $this->_use_found_rows = false;
        $this->csv_line_number = array();
        $this->csv_invalid = 0;
        
        parent::__construct();
        
        if (isset($_REQUEST['updatekb_pacbz_zone_zipcode_mapping'])) {
            $sql = 'SELECT zone_name FROM `' .
            _DB_PREFIX_.'kb_pacbz_zones` where id_kb_pacbz_zones ="' .pSQL($zone_id).'"';
            $zone_name = Db::getInstance()->ExecuteS($sql);
            $zipcode_mapping_id = Tools::getValue('id_kb_pacbz_zone_zipcode_mapping');
            $sql1 = 'SELECT * FROM `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping`' .
                    ' where id_kb_pacbz_zone_zipcode_mapping =' .pSQL($zipcode_mapping_id);
            $results = Db::getInstance()->ExecuteS($sql1);
            $formvalue = array();
            foreach ($results as $row) {
                $formvalue['zone_id'] = $row['zone_id'];
                $formvalue['deliver_by'] = $row['deliver_by'];
                $formvalue['zip-codes'] = $row['zipcode'];
                $formvalue['availability'] = $row['availability'];
            }
            $this->page_header_toolbar_title = $zone_name[0]['zone_name'].' : '.$formvalue['zip-codes'];
            
            $this->page_header_toolbar_btn['backkb_pacbz_zone_zipcode_mapping'] = array(
                'href' => 'index.php?controller=AdminViewZone&token='.Tools::getAdminTokenLite('AdminViewZone'),
                'desc' => $module->l('Back', 'AdminViewZone'),
                'icon' => 'process-icon-back'
            );
        
            parent::initPageHeaderToolbar();
        }
        
        if (isset($_REQUEST['addkb_pacbz_zone_zipcode_mapping'])) {
            $this->page_header_toolbar_btn['backkb_pacbz_zone_zipcode_mapping'] = array(
                'href' => 'index.php?controller=AdminViewZone&token='.Tools::getAdminTokenLite('AdminViewZone'),
                'desc' => $module->l('Back', 'AdminViewZone'),
                'icon' => 'process-icon-back'
            );
        
            parent::initPageHeaderToolbar();
        }
        
        //Field list
        $this->fields_list = array(
            'id_kb_pacbz_zone_zipcode_mapping' => array(
                'title' => $module->l('Zipcode ID', 'AdminViewZone'),
                'align' => 'center',
                'class' => 'fixed-width-lg',
            ),
            'zipcode' => array(
                'title' => $module->l('Zipcode Number', 'AdminViewZone'),
                'align' => 'center',
            ),
            'deliver_by' => array(
                'title' => $module->l('Deliver By', 'AdminViewZone'),
                'align' => 'center',
            ),
            'availability' => array(
                'title' => $module->l('Availability', 'AdminViewZone'),
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-xs',
                'align' => 'center',
            ),
        );
        
        if (Tools::getValue('success_add') == 1) {
            $this->confirmations = $module->l('Zip-code succesfully added.', 'AdminViewZone');
        }
        if (Tools::getValue('success_edit') == 1) {
            $this->confirmations = $module->l('Zip-code succesfully edit.', 'AdminViewZone');
        }
        if (Tools::getValue('success_delete') == 1) {
            $this->confirmations = $module->l('Zip-code succesfully deleted.', 'AdminViewZone');
        }
        
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $module->l('Delete selected', 'AdminViewZone'),
                'confirm' => $module->l('Delete selected items?', 'AdminViewZone'),
                'icon' => 'icon-trash'
            )
        );
    }
    
    /**
     * Function responsible to Add the edit and Delete button for the list generated from the construct function.
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function renderList()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        // Adds an Edit button for each result
        $this->addRowAction('edit');
  
        // Adds a Delete button for each result
        $this->addRowAction('delete');
    
        return parent::renderList();
    }

    /**
     * Function responsible to Add the "Add new" button on the "View Zone" admin page
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function initPageHeaderToolbar()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to display add and back button
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['backkb_pacbz_zone_zipcode_mapping'] = array(
                'href' => 'index.php?controller=AdminGlobalZones&token='.Tools::getAdminTokenLite('AdminGlobalZones'),
                //'desc' => $this->l('Back', null, null, false),
                'desc' => $module->l('Back', 'AdminViewZone'),
                'icon' => 'process-icon-back'
            );
        }
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['addkb_pacbz_zone_zipcode_mapping'] = array(
                'href' => self::$currentIndex . '&addkb_pacbz_zone_zipcode_mapping&token=' . $this->token,
                //'desc' => $this->l('Add new', null, null, false),
                'desc' => $module->l('Add new', 'AdminViewZone'),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }
    
    /**
     * Function responsible to Add the "Back" button on the "View Zone" admin page
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function initPageHeaderToolbarNew()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to display back button in same controller
        $this->page_header_toolbar_btn['backkb_pacbz_zone_zipcode_mapping'] = array(
            'href' => 'index.php?controller=AdminViewZone&token='.Tools::getAdminTokenLite('AdminViewZone'),
            'desc' => $module->l('Back', 'AdminViewZone'),
            'icon' => 'process-icon-back'
        );
        parent::initPageHeaderToolbar();
    }
    
    /**
     * Function responsible for Creating configuration setting form for "Add New View Zone" page
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function renderForm()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Display form in Add and Edit Zone page
        $this->initPageHeaderToolbarNew();
        if (Tools::getShopProtocol() == 'https://') {
            $av_protocol_name = _PS_BASE_URL_SSL_ . _MODULE_DIR_;
        } else {
            $av_protocol_name = _PS_BASE_URL_ . _MODULE_DIR_;
        }
        $this->context->controller->addJS($av_protocol_name . 'productavailabilitycheckbyzipcode/views/js/admin/zonevalidation.js');
        if (isset($_REQUEST['addkb_pacbz_zone_zipcode_mapping'])) {
            //Display Add Form
            $oss_admin_js = 'productavailabilitycheckbyzipcode/views/js/admin/kb_add_zone.js';
            $this->context->controller->addJS($av_protocol_name.$oss_admin_js);

            $formvalue = array();
            $languages = Language::getLanguages(true);
            $config = Configuration::get('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE_ADD_ZONE');
            $formvalue = json_decode($config, true);
            if (Tools::isSubmit('product_availability_check_by_zipcode')) {
                $formvalue = Tools::getvalue('product_availability_check_by_zipcode');
                $product_zipcode_array = Tools::getvalue('product_availability_check_by_zipcode');
                $formvalue['selected_file'] = $product_zipcode_array['selected_file'];
            }

            $availability = array(
                'label' => $module->l('Availability', 'AdminViewZone'),
                'type' => 'switch',
                'hint' => $module->l('Enter the availability status of the product at configured Zipcode.', 'AdminViewZone'),
                'name' => 'product_availability_check_by_zipcode[availability]',
                'required' => true,
                'values' => array(
                    array(
                        'value' => 1,
                        'id' => 'product_availability_check_by_zipcode[availability]_on',
                    ),
                    array(
                        'value' => 0,
                        'id' => 'product_availability_check_by_zipcode[availability]_off',
                    ),
                ),
            );

            $upload_csv = array(
                'label' => $module->l('Upload CSV File', 'AdminViewZone'),
                'type' => 'switch',
                'hint' => $module->l('Upload CSV File to add large number of zip-codes.', 'AdminViewZone'),
                'name' => 'product_availability_check_by_zipcode[upload_csv]',
                //'required' => true,
                'values' => array(
                    array(
                        'value' => 1,
                        'id' => 'product_availability_check_by_zipcode[upload_csv]_on',
                    ),
                    array(
                        'value' => 0,
                        'id' => 'product_availability_check_by_zipcode[upload_csv]_off',
                    ),
                ),
            );

            $this->fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $module->l('Add new Zipcode', 'AdminViewZone'),
                    ),
                    'input' => array(
                        $upload_csv,
                        $availability,
                        array(
                            'type' => 'textarea',
                            'label' => $module->l('Zip-Code', 'AdminViewZone'),
                            'name' => 'product_availability_check_by_zipcode[zip-codes]',
                            'hint' => $module->l('Please enter zip-codes.', 'AdminViewZone'),
                            'desc' => $module->l('To add multiple zip-codes use comma "," to separate. Ex- "  201222,201233,786999  "', 'AdminViewZone'),
                            'class' => 'optn_general test123',
                            'required' => true,
                            'maxlength' => 120,
                            'cols' => 100,
                            'rows' => 3
                        ),
                        array(
                            'type' => 'text',
                            'label' => $module->l('Deliver By', 'AdminViewZone'),
                            'name' => 'product_availability_check_by_zipcode[deliver_by]',
                            'hint' => $module->l('Enter number of days of delivery', 'AdminViewZone'),
                            'desc' => $module->l('Only positive integer value allowed.', 'AdminViewZone'),
                            'required' => true,
                            'class' => 'optn_lookfeel vss-textarea',
                            'cols' => 100,
                        ),
                        array(
                            'type' => 'file',
                            'label' => $module->l('Choose CSV File', 'AdminViewZone'),
                            'name' => 'product_availability_check_by_zipcode[selected_file]',
                            'id' => 'uploadedfile',
                            'required' => true,
                            'display_image' => true,
                            'desc' => $module->l('Download Sample of CSV File', 'AdminViewZone'),
                            'hint' => $module->l('Select the CSV File to be add zipcodes.', 'AdminViewZone'),
                        ),
                    ),
                    'submit' => array(
                        'title' => $module->l('Save', 'AdminViewZone'),
                        'class' => 'btn btn-default pull-right view_zone_add_new'
                    ),
                ),
            );

            if (isset($formvalue['product_availability_check_by_zipcode']['availability'])) {
                $de_available = $formvalue['product_availability_check_by_zipcode']['availability'];
                $de_upload_csv = $formvalue['product_availability_check_by_zipcode']['upload_csv'];
                $zipcodes = $formvalue['product_availability_check_by_zipcode']['zip-codes'];
                $deliver_by = $formvalue['product_availability_check_by_zipcode']['deliver_by'];
                $selected_file = $formvalue['product_availability_check_by_zipcode']['selected_file'];

                $field_value = array(
                    'product_availability_check_by_zipcode[availability]' => $de_available,
                    'product_availability_check_by_zipcode[upload_csv]' => $de_upload_csv,
                    'product_availability_check_by_zipcode[zip-codes]' => $zipcodes,
                    'product_availability_check_by_zipcode[deliver_by]' => $deliver_by,
                    'product_availability_check_by_zipcode[selected_file]' => $selected_file,
                );
            } else {
                $field_value = array(
                    'product_availability_check_by_zipcode[availability]' => $formvalue['availability'],
                    'product_availability_check_by_zipcode[upload_csv]' => $formvalue['upload_csv'],
                    'product_availability_check_by_zipcode[zip-codes]' => $formvalue['zip-codes'],
                    'product_availability_check_by_zipcode[deliver_by]' => $formvalue['deliver_by'],
                    'product_availability_check_by_zipcode[selected_file]' => $formvalue['selected_file'],
                );
            }

            $helper = new HelperForm();
            $helper->fields_value = $field_value;
            $languages = Language::getlanguages(true);
            foreach ($languages as $k => $language) {
                $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
            }

            $action = 'index.php?controller=AdminViewZone&addkb_pacbz_zone_zipcode_mapping&token='.$this->token;
            $helper->name_controller = $this->controller_name;
            $helper->languages = $languages;
            $helper->default_form_language = $this->context->language->id;
            $helper->token = Tools::getAdminTokenLite('AdminViewZone');
            $helper->currentIndex = 'index.php?controller=AdminViewZone&addkb_pacbz_zone_zipcode_mapping&token=' .
                $this->token;
            $helper->show_toolbar = true;
            $helper->toolbar_scroll = true;
            $helper->show_cancel_button = false;
            $helper->submit_action = $action;
            $helper->no_link = true;
            $form = $helper->generateForm(array($this->fields_form));

            $views_path = 'productavailabilitycheckbyzipcode/views/';
            $file_name = 'file/document_zipcode.csv';
            if (Tools::getShopProtocol() == 'https://') {
                $download_path = _PS_BASE_URL_SSL_ . _MODULE_DIR_ . $views_path .$file_name;
            } else {
                $download_path = _PS_BASE_URL_ . _MODULE_DIR_ . $views_path .$file_name;
            }

            $this->context->smarty->assign('download_path', $download_path);
            $content = $this->context->smarty->fetch(_PS_MODULE_DIR_.$views_path.'templates/admin/order_setting.tpl');

            return $form.$content;
        } elseif (isset($_REQUEST['updatekb_pacbz_zone_zipcode_mapping'])) {
            //$this->context->smarty->tpl_vars['breadcrumbs2']->value['tab']['name'] = 'hello';
            $formvalue = array();
            if (Tools::isSubmit('product_availability_check_by_zipcode')) {
                $formvalue = Tools::getvalue('product_availability_check_by_zipcode');
            }
            $zipcode_mapping_id = Tools::getValue('id_kb_pacbz_zone_zipcode_mapping');
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping`' .
                    ' where id_kb_pacbz_zone_zipcode_mapping ="' .pSQL($zipcode_mapping_id).'"';
            $results = Db::getInstance()->ExecuteS($sql);
            foreach ($results as $row) {
                $formvalue['zone_id'] = $row['zone_id'];
                $formvalue['deliver_by'] = $row['deliver_by'];
                $formvalue['zip-codes'] = $row['zipcode'];
                $formvalue['availability'] = $row['availability'];
            }
            $formvalue['id_kb_pacbz_zone_zipcode_mapping'] = $zipcode_mapping_id;

            $this->page_header_toolbar_title .= ' : '.$formvalue['zip-codes'];
            $languages = Language::getLanguages(true);
            if (Tools::isSubmit('product_availability_check_by_zipcode')) {
                $formvalue = Tools::getvalue('product_availability_check_by_zipcode');
            }
            
            $availability = array(
                'label' => $module->l('Availability', 'AdminViewZone'),
                'type' => 'switch',
                'hint' => $module->l('Enter the availability status of the product at configured Zipcode.', 'AdminViewZone'),
                'name' => 'product_availability_check_by_zipcode[availability]',
                'required' => true,
                'values' => array(
                    array(
                        'value' => 1,
                        'id' => 'product_availability_check_by_zipcode[availability]_on',
                    ),
                    array(
                        'value' => 0,
                        'id' => 'product_availability_check_by_zipcode[availability]_off',
                    ),
                ),
            );
            
            $this->fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $module->l('Edit Zipcode', 'AdminViewZone'),
                    ),
                    'input' => array(
                        $availability,
                        array(
                            'type' => 'text',
                            'label' => $module->l('Deliver By', 'AdminViewZone'),
                            'name' => 'product_availability_check_by_zipcode[deliver_by]',
                            'hint' => $module->l('Enter number of days of delivery', 'AdminViewZone'),
                            'desc' => $module->l('Only positive integer value allowed.', 'AdminViewZone'),
                            'class' => 'optn_lookfeel vss-textarea',
                            'cols' => 100,
                            'required' => true,
                        ),
                        array(
                            'type' => 'hidden',
                            'label' => $module->l('Zone ID', 'AdminViewZone'),
                            'name' => 'product_availability_check_by_zipcode[id_kb_pacbz_zone_zipcode_mapping]',
                            'class' => 'optn_general test123',
                        ),
                    ),
                    'submit' => array(
                        'title' => $module->l('Save', 'AdminViewZone'),
                        'class' => 'btn btn-default pull-right view_zone'
                    ),
                ),
            );
            
            if (isset($formvalue['product_availability_check_by_zipcode']['availability'])) {
                $de_available = $formvalue['product_availability_check_by_zipcode']['availability'];
                $deliver_by = $formvalue['product_availability_check_by_zipcode']['deliver_by'];
                $zip_map_id = $formvalue['product_availability_check_by_zipcode']['id_kb_pacbz_zone_zipcode_mapping'];
                $field_value = array(
                    'product_availability_check_by_zipcode[availability]' => $de_available,
                    'product_availability_check_by_zipcode[deliver_by]' => $deliver_by,
                    'product_availability_check_by_zipcode[id_kb_pacbz_zone_zipcode_mapping]' => $zip_map_id,
                );
            } else {
                $zipcode_map_id = $formvalue['id_kb_pacbz_zone_zipcode_mapping'];
                $field_value = array(
                    'product_availability_check_by_zipcode[availability]' => $formvalue['availability'],
                    'product_availability_check_by_zipcode[deliver_by]' => $formvalue['deliver_by'],
                    'product_availability_check_by_zipcode[id_kb_pacbz_zone_zipcode_mapping]' => $zipcode_map_id,
                );
            }
            
            $helper = new HelperForm();
            $helper->fields_value = $field_value;
            $languages = Language::getlanguages(true);
            foreach ($languages as $k => $language) {
                $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
            }

            $action = AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminViewZone');
            $helper->name_controller = $this->controller_name;
            $helper->languages = $languages;
            $helper->default_form_language = $this->context->language->id;
            $helper->token = Tools::getAdminTokenLite('AdminViewZone');
            $zone_id = $formvalue['id_kb_pacbz_zone_zipcode_mapping'];
            $helper->currentIndex = "index.php?controller=AdminViewZone&id_kb_pacbz_zone_zipcode_mapping=$zone_id&" .
                "updatekb_pacbz_zone_zipcode_mapping&token=".$this->token;
            $helper->show_toolbar = true;
            $helper->toolbar_scroll = true;
            $helper->show_cancel_button = false;
            $helper->submit_action = $action;
            $helper->no_link = true;
            $form = $helper->generateForm(array($this->fields_form));
            return $this->renderErrorTranslationsTemplate().$form;
        }
    }

    /**
     * Function responsible to handing the data validation, persistence, and saving of data in DB for "Add new" page
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function initProcess()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        /**
         * Below code is used to update the availability (Enable/Disable) of the zone, and is trigerred when admin click on availability of any zipcode
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        if (isset($_REQUEST['statuskb_pacbz_zone_zipcode_mapping']) &&
            $_REQUEST['id_kb_pacbz_zone_zipcode_mapping'] != null) {
            $sql = 'SELECT availability FROM `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` where' .
                    ' id_kb_pacbz_zone_zipcode_mapping ="'.pSQL($_REQUEST['id_kb_pacbz_zone_zipcode_mapping']).'"';
            $results = Db::getInstance()->ExecuteS($sql);
            if (isset($results[0]['availability']) && $results[0]['availability'] == 1) {
                $results[0]['availability'] = 0;
            } else {
                $results[0]['availability'] = 1;
            }
            $sql4 = 'UPDATE `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` SET ' .
                'availability = "'.pSQL($results[0]['availability']).
                '" where id_kb_pacbz_zone_zipcode_mapping = "' .
                pSQL($_REQUEST['id_kb_pacbz_zone_zipcode_mapping']).'"';
            Db::getInstance()->execute($sql4);
            $this->errors[] = null;
            $this->confirmations = $module->l('Status Updated.', 'AdminViewZone');
        }
        /**
         * Below code is used to validating and saving the data for "Add new Zipcode" page, and is triggered whenwver we click the save button on the "Add new zipcode" page
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        if (isset($_REQUEST['addkb_pacbz_zone_zipcode_mapping'])) {
            //Perform validation when Add Zone form is submitted
            /**
             * Code responsible for Validating the "Add new" page form Data
             * @date 14-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
            if (Tools::isSubmit('product_availability_check_by_zipcode')) {
                $formvalue = Tools::getvalue('product_availability_check_by_zipcode');
                $error_count = 0;
                if ($formvalue['upload_csv'] == 0) {
                    /*Knowband validation start*/
                    if (!empty($formvalue['zip-codes'])) {
                        $pv_zip_codes = explode(",", $formvalue['zip-codes']);
                        $pv_zip_codes = array_map('trim', $pv_zip_codes);
                        $pv_zip_codes = array_unique($pv_zip_codes);
                        $pv_zip_codes = array_filter($pv_zip_codes);
                        
                        $i = 0;
                        foreach ($pv_zip_codes as $pv_zip_code) {
                            if (Tools::strlen((string)$pv_zip_code) <= 10) {
                                if (Validate::isPostCode($pv_zip_code) == false) {
                                    $this->errors[] = $module->l('Please enter valid zip-code.', 'AdminViewZone');
                                    $error_count++;
                                    break;
                                }
                                
                                $sql1 = 'SELECT m.zipcode,z.zone_name FROM `'._DB_PREFIX_.'kb_pacbz_zones`' .
                                    ' as z inner join `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` as m on' .
                                    ' z.id_kb_pacbz_zones = m.zone_id where zipcode ="' .pSQL($pv_zip_code).'" and z.is_seller_zone = "0"';
                                $results = Db::getInstance()->ExecuteS($sql1);
                                if ($results != null) {
                                    foreach ($results as $result) {
                                        $erro1 = $module->l('Zipcode ', 'AdminViewZone');
                                        $erro2 = $module->l(' already exist in zone', 'AdminViewZone');
                                        $this->errors[] = $erro1." ".$result['zipcode']." ". $erro2 ." ". $result['zone_name'];
                                        $error_count++;
                                        break;
                                    }
                                }
                            } else {
                                $this->errors[] = $module->l('Please enter valid zip-code.', 'AdminViewZone');
                                $error_count++;
                                break;
                            }
                            $i++;
                        }
                    } else {
                        $this->errors[] = $module->l('Please enter at least one zip-code.', 'AdminViewZone');
                        $error_count++;
                    }
                    /*Knowband validation end*/
                    /*Knowband validation start*/
                    if ($formvalue['deliver_by'] != null) {
                        $formvalue['deliver_by'] = trim($formvalue['deliver_by']);
                        if (preg_match('/^\d+$/', $formvalue['deliver_by']) && $formvalue['deliver_by'] > 0) {
                            if (Tools::strlen((string)$formvalue['deliver_by']) > 3) {
                                $this->errors[] = $module->l('Number of days must be less than 1000.', 'AdminViewZone');
                                $error_count++;
                            }
                        } else {
                            $this->errors[] = $module->l('Only positive integer value allowed.', 'AdminViewZone');
                            $error_count++;
                        }
                    } else {
                        $this->errors[] = $module->l('Please enter number of days of delivery.', 'AdminViewZone');
                        $error_count++;
                    }
                    /*Knowband validation end*/
                    /*Knowband validation start*/
                } else {
                    $product_zipcode_array = Tools::getvalue('product_availability_check_by_zipcode');
                    $formvalue['selected_file'] = $product_zipcode_array['selected_file'];
                    if ($formvalue['selected_file'] == null) {
                        $this->errors[] .= $module->l('Please upload the CSV file.', 'AdminViewZone');
                        $error_count++;
                    } else {
                        if ($_FILES['product_availability_check_by_zipcode']['name']['selected_file'] != null) {
                            $file_name = $_FILES['product_availability_check_by_zipcode']['name']['selected_file'];
                            $file_size = $_FILES['product_availability_check_by_zipcode']['size']['selected_file'];
                            $file_tmp = $_FILES['product_availability_check_by_zipcode']['tmp_name']['selected_file'];

                            $x=explode('.', $file_name);
                            $file_ext = Tools::strtolower(end($x));
                            $expensions = array("csv");
                            $formvalue['selected_file'] = "sample_add_zipcodes.".$file_ext;


                            if (in_array($file_ext, $expensions) === false) {
                                $this->errors[] .= $module->l('Please choose file with .csv extension.', 'AdminViewZone');
                                $error_count++;
                            }

                            if ($file_size >= 4194304) {
                                $this->errors[] .= $module->l('File size must be less than 4 MB.', 'AdminViewZone');
                                $error_count++;
                            }

                            if ($error_count == 0) {
                                if ($_FILES['product_availability_check_by_zipcode']['name']['selected_file'] != null) {
                                    $file_path1 = 'productavailabilitycheckbyzipcode/views/file/' ;
                                    move_uploaded_file(
                                        $file_tmp,
                                        _PS_MODULE_DIR_.$file_path1."sample_add_zipcodes.".$file_ext
                                    );
                                    chmod(_PS_MODULE_DIR_ . $file_path1 . "sample_add_zipcodes." . $file_ext, 0755);
                                }

//                                $csv_file_array = $this->readCSVtoArray("sample_add_zipcodes.".$file_ext);
                                
                                $path = _PS_MODULE_DIR_ . $file_path1 . "sample_add_zipcodes." . $file_ext;
                                $csv_file_array = $this->readCSVtoArray($path, ',');

                                if ($this->validateCSVFile($csv_file_array) == false) {
                                    // In case of incorrect data csv data
                                    if ($this->csv_invalid == 1) {
                                        $this->errors[] .= $module->l('Invalid content inside CSV file.Please check the sample document.', 'AdminViewZone');
                                        $this->csv_invalid = 0;
                                    } else {
                                        $pv_part1 = $module->l('Please check ', 'AdminViewZone');
                                        $pv_part2 = $module->l('th row of CSV File.Following are the validation rules-', 'AdminViewZone');
                                        $pv_middle = implode(",", array_unique($this->csv_line_number));
                                        $this->errors[] .= $pv_part1 ." ". $pv_middle . $pv_part2;
                                        $this->errors[] .= $module->l('Zipcode must be non-empty,less than 10 digits and must not contain any special characters.', 'AdminViewZone');
                                        $this->errors[] .= $module->l('Deliver by must be non-empty and contain only positive integer value.', 'AdminViewZone');
                                        $this->errors[] .= $module->l('Availability must be non-empty and contains 1 for Yes or 0 or No. ', 'AdminViewZone');
                                        $this->errors[] .= $module->l('Zipcode must be unique.', 'AdminViewZone');
                                        
                                        $this->csv_line_number = null;
                                    }
                                    $error_count++;
                                }
                            }
                        }
                    }
                }
                /*Knowband validation end*/
                /**
                 * Code responsible for Saving the "Add new Zipcode" page form in DB if no error found
                 * @date 14-02-2023
                 * @author 
                 * @commenter Vishal Goyal
                 */
                if ($error_count == 0) {
                    //Inserting values if no error occurs
                    $zone_id = $this->context->cookie->zone_cookie2;
                    /**
                     * Code responsible for Saving the "Add new Zipcode" page form in DB if "Upload CSV File" is disabled
                     * @date 14-02-2023
                     * @author 
                     * @commenter Vishal Goyal
                     */
                    if ($formvalue['upload_csv'] == 0) {
                        $pv_zip_codes = explode(",", $formvalue['zip-codes']);
                        $pv_zip_codes = array_map('trim', $pv_zip_codes);
                        $pv_zip_codes = array_unique($pv_zip_codes);
                        $pv_zip_codes = array_filter($pv_zip_codes);
                        $i = 0;
                        foreach ($pv_zip_codes as $pv_zip_code) {
                            $sql3 = 'INSERT INTO `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping`' .
                                '(`id_kb_pacbz_zone_zipcode_mapping`, `zone_id`, `zipcode`,`deliver_by` ,' .
                                '`availability`) VALUES ("" ,"'.pSQL($zone_id).'", "'.pSQL($pv_zip_code).'" ,' .
                                ' "'.pSQL($formvalue['deliver_by']).'" , "'.pSQL($formvalue['availability']).'")';
                            Db::getInstance()->execute($sql3);
                            $i++;
                        }
                    } elseif ($formvalue['upload_csv'] == 1) {
                        /**
                         * Code responsible for Saving the "Add new Zipcode" page form in DB if "Upload CSV File" is enabled and we have uploaded the CSV file
                         * @date 14-02-2023
                         * @author 
                         * @commenter Vishal Goyal
                         */
                        $this->insertCsvData($csv_file_array, $zone_id);
                    }
                    
                    Tools::redirectAdmin('index.php?controller=AdminViewZone&success_add=1&token='.$this->token);
                }
            }
        } elseif (isset($_REQUEST['updatekb_pacbz_zone_zipcode_mapping'])) {
            /**
             * Below code is used to validating and saving the data for "Edit Zipcode" page, and is triggered whenwver we click the save button on the "Edit Zipcode" page
             * @date 14-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
            //Perform validation when Edit Zone form is submitted
            if (Tools::isSubmit('product_availability_check_by_zipcode')) {
                $formvalue = Tools::getvalue('product_availability_check_by_zipcode');
                $error_count = 0;
                /*Knowband validation start*/
                if ($formvalue['deliver_by'] != null) {
                    $formvalue['deliver_by'] = trim($formvalue['deliver_by']);
                    if (preg_match('/^\d+$/', $formvalue['deliver_by']) && $formvalue['deliver_by'] > 0) {
                        if (Tools::strlen((string)$formvalue['deliver_by']) > 3) {
                            $this->errors[] = $module->l('Number of days must be less than 1000.', 'AdminViewZone');
                            $error_count++;
                        }
                    } else {
                        $this->errors[] = $module->l('Only positive integer value allowed.', 'AdminViewZone');
                        $error_count++;
                    }
                } else {
                    $this->errors[] = $module->l('Please enter number of days of delivery.', 'AdminViewZone');
                    $error_count++;
                }
                /*Knowband validation end*/
                
                if ($error_count == 0) {
                    //Inserting values if no error occurs
                    $sql4 = 'UPDATE `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` SET ' .
                        'availability = "'.pSQL($formvalue['availability']).
                        '" ,deliver_by="'.pSQL($formvalue['deliver_by']).'" where id_kb_pacbz_zone_zipcode_mapping ="' .
                        pSQL($formvalue['id_kb_pacbz_zone_zipcode_mapping']).'"';
                    Db::getInstance()->execute($sql4);
                    Tools::redirectAdmin('index.php?controller=AdminViewZone&success_edit=1&token='.$this->token);
                }
            }
        }
        parent::initProcess();
    }
    
    /**
     * Function responsible to Adding the CSV file data in the "kb_pacbz_zone_zipcode_mapping" table.
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function insertCsvData($csv_file_array, $zone_id)
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to insert data from csv file
        $i = 0;
        
        unset($csv_file_array[0]);
        
        foreach ($csv_file_array as $pv_zip_code) {
            if (is_array($pv_zip_code)) {
                $pv_zip_code = array_map('trim', $pv_zip_code);

                $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'kb_pacbz_zone_zipcode_mapping`' .
                        ' where zipcode ="' . pSQL($pv_zip_code[0]) . '"';
                $results = Db::getInstance()->ExecuteS($sql);

                if ($results == null) {
                    $sql2 = 'INSERT INTO `' . _DB_PREFIX_ . 'kb_pacbz_zone_zipcode_mapping`(' .
                            '`id_kb_pacbz_zone_zipcode_mapping`, `zone_id`, `zipcode`' .
                            ',`deliver_by` ,`availability`) VALUES ("" ,"' . pSQL($zone_id) . '", "' .
                            pSQL($pv_zip_code[0]) . '" , "' . pSQL($pv_zip_code[1]) . '" , "' . pSQL($pv_zip_code[2]) . '")';
                    Db::getInstance()->execute($sql2);
                } else {
                    // Update the value if admin already added that zipcode
                    $sql = 'UPDATE `' . _DB_PREFIX_ . 'kb_pacbz_zone_zipcode_mapping` SET ' .
                            'deliver_by = "' . pSQL($pv_zip_code[1]) . '" , availability="' . pSQL($pv_zip_code[2]) .
                            '"  WHERE zipcode = "' . pSQL($pv_zip_code[0]) . '"';
                    Db::getInstance()->execute($sql);
                }
            }
        }
    }
    
    /**
     * Function responsible to Validating the data in CSV files and display the error if any exist.
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function validateCSVFile($csv_file_array)
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to validate data of csv file
        //$zone_id = $this->context->cookie->zone_cookie2;
        $avoid_first_row = 0;
        $false_flag = 0;
        foreach ($csv_file_array as $formvalue) {
            if (count($csv_file_array)-1 > $avoid_first_row) {
                if ($avoid_first_row != 0) {
                    if (count($formvalue) <= 3) {
                        $formvalue = array_map('trim', $formvalue);
                        if (isset($formvalue[0]) && $formvalue[0] != null) {
                            $pv_zip_codes = explode(",", $formvalue[0]);
                            $i = 0;
                            foreach ($pv_zip_codes as $pv_zip_code) {
                                $pv_zip_code = trim($pv_zip_code);
                                if (Tools::strlen((string)$pv_zip_code) <= 10) {
                                    if (Validate::isPostCode($pv_zip_code) == false) {
                                        $this->csv_line_number[] .= $avoid_first_row +1;
                                        $false_flag = 1;
                                    }

                                    $sql = 'SELECT * FROM `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping`' .
                                        ' where zipcode ="'.pSQL($pv_zip_code).'"';

                                    $results = Db::getInstance()->ExecuteS($sql);

                                    if ($results != null) {
                                        $this->csv_line_number[] .= $avoid_first_row +1;
                                        $false_flag = 1;
                                    }
                                } else {
                                    $this->csv_line_number[] .= $avoid_first_row +1;
                                    $false_flag = 1;
                                }
                                $i++;
                            }
                        } else {
                            $this->csv_line_number[] .= $avoid_first_row +1;
                            $false_flag = 1;
                        }

                        if (isset($formvalue[1]) && $formvalue[1] != null) {
                            if (preg_match('/^\d+$/', $formvalue[1]) && $formvalue[1] > 0) {
                                if (Tools::strlen((string)$formvalue[1]) > 3) {
                                    $this->csv_line_number[] .= $avoid_first_row +1;
                                    $false_flag = 1;
                                }
                            } else {
                                $this->csv_line_number[] .= $avoid_first_row +1;
                                $false_flag = 1;
                            }
                        } else {
                            $this->csv_line_number[] .= $avoid_first_row +1;
                            $false_flag = 1;
                        }

                        if (isset($formvalue[2]) && $formvalue[2] != null) {
                            if (!($formvalue[2] == 1 || $formvalue[2] == 0)) {//d(7);
                                $this->csv_line_number[] .= $avoid_first_row +1;
                                $false_flag = 1;
                            }
                        } else {
                            $this->csv_line_number[] .= $avoid_first_row +1;
                            $false_flag = 1;
                        }
                    } else {
                        $this->csv_invalid = 1;
                        $false_flag = 1;
                    }
                }
            }
            $avoid_first_row++;
        }
        
        if ($false_flag == 1) {
            return false;
        } else {
            return true;
        }
    }
    
//    public function readCSVtoArray($csv_file)
//    {
//        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
//        //Function to read data from csv file
//        if (Tools::getShopProtocol() == 'https://') {
//            $file_path = _PS_BASE_URL_SSL_._MODULE_DIR_.'productavailabilitycheckbyzipcode/views/file/';
//        } else {
//            $file_path = _PS_BASE_URL_._MODULE_DIR_.'productavailabilitycheckbyzipcode/views/file/';
//        }
//
//        $csvData = Tools::file_get_contents($file_path.$csv_file);
//        $lines = explode("\n", $csvData);
//        $array = array();
//        foreach ($lines as $line) {
//            $array[] = str_getcsv($line);
//        }
//        return $array;
//    }
    
    /**
     * Function responsible for converting the imported CSV data to the array format for further operations.
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return array CSV data array
     */
    public function readCSVtoArray($csv_file, $delemietr)
    {
        $file_handle = fopen($csv_file, 'r');
        $line_of_text = array();
        while (!feof($file_handle)) {
            $line_of_text[] = fgetcsv($file_handle, '10000', $delemietr);
        }
        fclose($file_handle);
        return $line_of_text;
    }
    
    /**
     * Function responsible to delete the existing zipcode-zone Mapping, triggered when we click the delete button on the "View Zone" page   
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function processDelete()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to delete zipcode and zone mapping
        $zipcode_mapping_id = Tools::getValue('id_kb_pacbz_zone_zipcode_mapping');
        if (isset($_REQUEST['deletekb_pacbz_zone_zipcode_mapping'])) {
            $sql = 'DELETE FROM `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` WHERE' .
                ' id_kb_pacbz_zone_zipcode_mapping = "'.pSQL($zipcode_mapping_id).'" ';
            Db::getInstance()->execute($sql);

            //Delete zone if all the zipcodes are deleted in a zone
            $sql = 'SELECT * 
                FROM  `'._DB_PREFIX_.'kb_pacbz_zones` pz
                LEFT JOIN  `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` pzz ON pz.id_kb_pacbz_zones = pzz.zone_id
                WHERE pzz.zone_id IS NULL ';
            $empty_zone_id = Db::getInstance()->ExecuteS($sql);
            if ($empty_zone_id != null) {
                Tools::redirectAdmin('index.php?controller=AdminGlobalZones&success_delete_all_zip=1&token='.Tools::getAdminTokenLite('AdminGlobalZones'));
            }
            Tools::redirectAdmin('index.php?controller=AdminViewZone&success_delete=1&token='.$this->token);
        }
    }
    
    /**
     * Function responsible to delete the bulk zipcode-zone Mapping.
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function processBulkDelete()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to delete zipcode and zone mapping in bulk
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $zipcode_mapping_id) {
                $sql = 'DELETE FROM `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` WHERE' .
                    ' id_kb_pacbz_zone_zipcode_mapping = "'.pSQL($zipcode_mapping_id).'" ';
                Db::getInstance()->execute($sql);
            }

            $sql = 'SELECT * 
                FROM  `'._DB_PREFIX_.'kb_pacbz_zones` pz
                LEFT JOIN  `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` pzz ON pz.id_kb_pacbz_zones = pzz.zone_id
                WHERE pzz.zone_id IS NULL ';
            $empty_zone_id = Db::getInstance()->ExecuteS($sql);
            if ($empty_zone_id != null) {
                Tools::redirectAdmin('index.php?controller=AdminGlobalZones&success_delete_all_zip=1&token='.Tools::getAdminTokenLite('AdminGlobalZones'));
            }

            Tools::redirectAdmin('index.php?controller=AdminViewZone&success_delete=1&token='.$this->token);
        }
    }
    
    public function renderErrorTranslationsTemplate()
    {
        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'productavailabilitycheckbyzipcode/views/templates/admin/_configure/helpers/form/gloal_zone_variables.tpl'
        );
    }
}
