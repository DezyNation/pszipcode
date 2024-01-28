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

include_once(_PS_MODULE_DIR_.'productavailabilitycheckbyzipcode/classes/GlobalZone.php');
class AdminGlobalZonesController extends ModuleAdminControllerCore
{
    
    /**
     * Function responsible to Creating list for all the available zones
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function __construct()
    {
        //Redirecting visitor to AdminViewZone Controller when view is clicked
        if (Tools::getValue('id_kb_pacbz_zones') != null && isset($_REQUEST['viewkb_pacbz_zones'])) {
            $linkobj = new Link();
            $red_path = $linkobj->getAdminLink('AdminViewZone').'&id_kb_pacbz_zones=' .
                Tools::getValue('id_kb_pacbz_zones');
            Tools::redirectAdmin($red_path);
        }
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Creating Zone List
        $this->context = Context::getContext();
        $this->table = 'kb_pacbz_zones';
        $this->className = 'GlobalZone';
        $this->show_toolbar = true;
        $this->list_no_link = true;
        $this->csv_line_number = array();
        $this->csv_invalid = 0;
        $this->bootstrap = true;
        $this->_select = '`id_kb_pacbz_zones`,`zone_name`,count(*) as zip_count';
        $this->_join = 'INNER JOIN `' . _DB_PREFIX_ . 'kb_pacbz_zone_zipcode_mapping` z ON ' .
            'a.id_kb_pacbz_zones = z.zone_id';
        $this->_where .= 'AND a.is_seller_zone = "0" AND id_shop=' . Context::getContext()->shop->id;
        $this->_group = 'GROUP BY z.zone_id';
        
        parent::__construct();

        if (isset($_REQUEST['addkb_pacbz_zones'])) {
            $this->page_header_toolbar_title = $module->l('Add new zone', 'AdminGlobalZones');
            $this->page_header_toolbar_btn['backkb_pacbz_zones'] = array(
                'href' => 'index.php?controller=AdminGlobalZones&token='.Tools::getAdminTokenLite('AdminGlobalZones'),
                'desc' => $module->l('Back', 'AdminGlobalZones'),
                'icon' => 'process-icon-back'
            );
        
            parent::initPageHeaderToolbar();
        }
        
        if (isset($_REQUEST['updatekb_pacbz_zones'])) {
            $this->zone_id = Tools::getValue('id_kb_pacbz_zones');
            $sql = 'SELECT zone_name FROM `'._DB_PREFIX_.'kb_pacbz_zones`' .
                    ' where id_kb_pacbz_zones =' .pSQL($this->zone_id);
            $results = Db::getInstance()->ExecuteS($sql);
            $formvalue = array();
            foreach ($results as $row) {
                $formvalue['zone_name'] = $row['zone_name'];
            }
            $this->page_header_toolbar_title = $formvalue['zone_name'];
            
            $this->page_header_toolbar_btn['backkb_pacbz_zones'] = array(
                'href' => 'index.php?controller=AdminGlobalZones&token='.Tools::getAdminTokenLite('AdminGlobalZones'),
                'desc' => $module->l('Back', 'AdminGlobalZones'),
                'icon' => 'process-icon-back'
            );
        
            parent::initPageHeaderToolbar();
        }

        $this->fields_list = array(
            'id_kb_pacbz_zones' => array(
                'title' => $module->l('Zone ID', 'AdminGlobalZones'),
                'align' => 'center',
                'class' => 'fixed-width-lg',
            ),
            'zone_name' => array(
                'title' => $module->l('Zone Name', 'AdminGlobalZones'),
                'align' => 'center',
            ),
            'zip_count' => array(
                'title' => $module->l('Total Zipcodes', 'AdminGlobalZones'),
                'align' => 'center',
                'havingFilter' => true
            ),
        );

        //Display success messages
        if (Tools::getValue('success_add') == 1) {
            $this->confirmations = $module->l('Zone succesfully added.', 'AdminGlobalZones');
        }
        if (Tools::getValue('success_edit') == 1) {
            $this->confirmations = $module->l('Zone succesfully edit.', 'AdminGlobalZones');
        }
        if (Tools::getValue('success_delete') == 1) {
            $this->confirmations = $module->l('Zone succesfully deleted.', 'AdminGlobalZones');
        }
        if (Tools::getValue('success_delete_all_zip') == 1) {
            $this->confirmations = $module->l('Zone succesfully deleted with zipcodes.', 'AdminGlobalZones');
             $sql = 'SELECT * 
                FROM  `'._DB_PREFIX_.'kb_pacbz_zones` pz
                LEFT JOIN  `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` pzz ON pz.id_kb_pacbz_zones = pzz.zone_id
                WHERE pzz.zone_id IS NULL ';
            $empty_zone_id = Db::getInstance()->ExecuteS($sql);
            if ($empty_zone_id != null) {
                foreach ($empty_zone_id as $zipcode_mapping_id) {
                    $sql = 'DELETE FROM `'._DB_PREFIX_.'kb_pacbz_zones` WHERE' .
                        ' id_kb_pacbz_zones = "'.pSQL($zipcode_mapping_id['id_kb_pacbz_zones']).'" ';
                    Db::getInstance()->execute($sql);
                }
            }
        }

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $module->l('Delete selected', 'AdminGlobalZones'),
                'confirm' => $module->l('Delete selected items?', 'AdminGlobalZones'),
                'icon' => 'icon-trash'
            )
        );
    }
    
    /**
     * Function responsible to Add the Edit, view and Delete button for the list generated from the construct function.
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function renderList()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        // Adds an View button for each result
        $this->addRowAction('view');
        // Adds an Edit button for each result
        $this->addRowAction('edit');
        // Adds a Delete button for each result
        $this->addRowAction('delete');
  
        return parent::renderList();
    }

    /**
     * Function responsible to Add the "Add new Zone" button on the GLobal zone admin page
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function initPageHeaderToolbar()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to display add button in same controller
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['addkb_pacbz_zones'] = array(
                'href' => self::$currentIndex . '&addkb_pacbz_zones&token=' . $this->token,
                'desc' => $module->l('Add new zone', 'AdminGlobalZones'),
                'icon' => 'process-icon-new'
            );
        }
        parent::initPageHeaderToolbar();
    }

    
    /**
     * Function responsible to Add the "Back" button on the "Add new Zone" admin page
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function initPageHeaderToolbarNew()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to display back button in same controller
        $this->page_header_toolbar_btn['backaddkb_pacbz_zones'] = array(
            'href' => 'index.php?controller=AdminGlobalZones&token='.Tools::getAdminTokenLite('AdminGlobalZones'),
            //'desc' => $this->l('Back', null, null, false),
            'desc' => $module->l('Back', 'AdminGlobalZones'),
            'icon' => 'process-icon-back'
        );
        
        parent::initPageHeaderToolbar();
    }
    
    
    /**
     * Function responsible for Creating configuration setting form for "Add new Zone" page
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
        if (isset($_REQUEST['addkb_pacbz_zones'])) {
            //Display Add Form
            $this->page_header_toolbar_title = $module->l('Add new zone', 'AdminGlobalZones');
            $oss_admin_js = 'productavailabilitycheckbyzipcode/views/js/admin/kb_add_zone.js';
            $this->context->controller->addJS($av_protocol_name.$oss_admin_js);

            $formvalue = array();
            $languages = Language::getLanguages(true);
            $config = Configuration::get('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE_ADD_ZONE');
            $formvalue = json_decode($config, true);
            if (Tools::isSubmit('product_availability_check_by_zipcode')) {
                $formvalue = Tools::getvalue('product_availability_check_by_zipcode');
                $product_zipcode_array = Tools::getvalue('product_availability_check_by_zipcode');
//                $formvalue['selected_file'] = $product_zipcode_array['selected_file'];
            }

            $availability = array(
                'label' => $module->l('Availability', 'AdminGlobalZones'),
                'type' => 'switch',
                'hint' => $module->l('Enter the availability status of the product at configured Zipcode.', 'AdminGlobalZones'),
                'name' => 'product_availability_check_by_zipcode[availability]',
                //'required' => true,
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
                        'title' => $module->l('Add new Zone', 'AdminGlobalZones'),
                    ),
                    'input' => array(
                        $availability,
                        array(
                            'type' => 'text',
                            'label' => $module->l('Zone Name', 'AdminGlobalZones'),
                            'name' => 'product_availability_check_by_zipcode[zone_name]',
                            'hint' => $module->l('Enter define the zone name.', 'AdminGlobalZones'),
                            'desc' => $module->l('Only 50 characters allowed.', 'AdminGlobalZones'),
                            'class' => 'optn_general test123',
                            'required' => true,
                            'maxlength' => 50,
                            'cols' => 100,
                        ),
                        array(
                            'type' => 'textarea',
                            'label' => $module->l('Zip-Code', 'AdminGlobalZones'),
                            'name' => 'product_availability_check_by_zipcode[zip-codes]',
                            'hint' => $module->l('Please enter zip-codes.', 'AdminGlobalZones'),
                            'desc' => $module->l('To add multiple zip-codes use comma "," to separate. Ex- "201222,201233,786999"', 'AdminGlobalZones'),
                            'class' => 'optn_general test123',
                            'required' => true,
                            'maxlength' => 120,
                            'cols' => 100,
                            'rows' => 3
                        ),
                        array(
                            'type' => 'text',
                            'label' => $module->l('Deliver By', 'AdminGlobalZones'),
                            'name' => 'product_availability_check_by_zipcode[deliver_by]',
                            'hint' => $module->l('Enter number of days of delivery', 'AdminGlobalZones'),
                            'desc' => $module->l('Only positive integer value allowed.', 'AdminGlobalZones'),
                            'class' => 'optn_lookfeel vss-textarea',
                            'cols' => 100,
                            'required' => true,
                        ),
                    ),
                    'submit' => array(
                        'title' => $module->l('Save', 'AdminGlobalZones'),
                        'class' => 'btn btn-default pull-right zones_add_btn'
                    ),
                ),
            );

            if (isset($formvalue['product_availability_check_by_zipcode']['availability'])) {
                $de_available = $formvalue['product_availability_check_by_zipcode']['availability'];
                $zone_name = $formvalue['product_availability_check_by_zipcode']['zone_name'];
                $zipcodes = $formvalue['product_availability_check_by_zipcode']['zip-codes'];
                $deliver_by = $formvalue['product_availability_check_by_zipcode']['deliver_by'];
                
                $field_value = array(
                    'product_availability_check_by_zipcode[availability]' => $de_available,
                    'product_availability_check_by_zipcode[zone_name]' => $zone_name,
                    'product_availability_check_by_zipcode[zip-codes]' => $zipcodes,
                    'product_availability_check_by_zipcode[deliver_by]' => $deliver_by,
                );
            } else {
                $field_value = array(
                    'product_availability_check_by_zipcode[availability]' => $formvalue['availability'],
                    'product_availability_check_by_zipcode[zone_name]' => $formvalue['zone_name'],
                    'product_availability_check_by_zipcode[zip-codes]' => $formvalue['zip-codes'],
                    'product_availability_check_by_zipcode[deliver_by]' => $formvalue['deliver_by'],
                );
            }

            $helper = new HelperForm();
            $helper->fields_value = $field_value;
            $languages = Language::getlanguages(true);
            foreach ($languages as $k => $language) {
                $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
            }

            $action = AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminGlobalZones');
            $helper->name_controller = $this->controller_name;
            $helper->languages = $languages;
            $helper->default_form_language = $this->context->language->id;
            $helper->token = Tools::getAdminTokenLite('AdminGlobalZones');
            $helper->currentIndex = 'index.php?controller=AdminGlobalZones&addkb_pacbz_zones&token='.$this->token;
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
        } elseif (isset($_REQUEST['updatekb_pacbz_zones'])) {
            //Display Edit Form
            $formvalue = array();
            $this->zone_id = Tools::getValue('id_kb_pacbz_zones');
            $sql = 'SELECT zone_name FROM `'._DB_PREFIX_.'kb_pacbz_zones`' .
                    ' where id_kb_pacbz_zones ="' .pSQL($this->zone_id).'"';
            $results = Db::getInstance()->ExecuteS($sql);
            foreach ($results as $row) {
                $formvalue['zone_name'] = $row['zone_name'];
            }
            $this->page_header_toolbar_title = $formvalue['zone_name'];
            $formvalue['zone_id'] = $this->zone_id;
            
            $languages = Language::getLanguages(true);
            if (Tools::isSubmit('product_availability_check_by_zipcode')) {
                $formvalue = Tools::getvalue('product_availability_check_by_zipcode');//d($formvalue);
            }
            
            $this->fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $module->l('Edit Zone', 'AdminGlobalZones'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'text',
                            'label' => $module->l('Zone Name', 'AdminGlobalZones'),
                            'name' => 'product_availability_check_by_zipcode[zone_name]',
                            'hint' => $module->l('Enter define the zone name.', 'AdminGlobalZones'),
                            'desc' => $module->l('Only 50 characters allowed.', 'AdminGlobalZones'),
                            'class' => 'optn_general test123 zonename',
                            'required' => true,
                            'maxlength' => 50,
                            'cols' => 100,
                        ),
                        array(
                            'type' => 'hidden',
                            'label' => $module->l('Zone ID', 'AdminGlobalZones'),
                            'name' => 'product_availability_check_by_zipcode[zone_id]',
                            'class' => 'optn_general test123',
                        ),
                    ),
                    'submit' => array(
                        'title' => $module->l('Save', 'AdminGlobalZones'),
                        'class' => 'btn btn-default pull-right zones_edit_btn',
                    ),
                ),
            );
            
            if (isset($formvalue['product_availability_check_by_zipcode']['zone_name'])) {
                $zone_name = $formvalue['product_availability_check_by_zipcode']['zone_name'];
                $zone_id = $formvalue['product_availability_check_by_zipcode']['zone_id'];
                
                $field_value = array(
                    'product_availability_check_by_zipcode[zone_name]' => $zone_name,
                    'product_availability_check_by_zipcode[zone_id]' => $zone_id,
                );
            } else {
                $field_value = array(
                    'product_availability_check_by_zipcode[zone_name]' => $formvalue['zone_name'],
                    'product_availability_check_by_zipcode[zone_id]' => $formvalue['zone_id'],
                );
            }
            
            $helper = new HelperForm();
            $helper->fields_value = $field_value;
            $languages = Language::getlanguages(true);
            foreach ($languages as $k => $language) {
                $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
            }

            $action = AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminGlobalZones');
            $helper->name_controller = $this->controller_name;
            $helper->languages = $languages;
            $helper->default_form_language = $this->context->language->id;
            $helper->token = Tools::getAdminTokenLite('AdminGlobalZones');
            $zone_id = $formvalue['zone_id'];
            $helper->currentIndex = "index.php?controller=AdminGlobalZones&id_kb_pacbz_zones=$zone_id" .
                "&updatekb_pacbz_zones&token=".$this->token;
            $helper->show_toolbar = true;
            $helper->toolbar_scroll = true;
            $helper->show_cancel_button = false;
            $helper->submit_action = $action;
            $helper->no_link = true;
            $form = $helper->generateForm(array($this->fields_form));
            return  $this->renderErrorTranslationsTemplate().$form;
        }
    }

    
    /**
     * Function responsible to handing the data validation, persistence, and saving of data in DB for GLobal Zone "Add new Zone" and "edit Zone" page
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function initProcess()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        /**
         * Below code is used to validating and saving the data for "Add new zone" page, and is triggered whenwver we click the save button on the "Add new zone" page
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        if (isset($_REQUEST['addkb_pacbz_zones'])) {
            //Perform validation when Add Zone form is submitted
            if (Tools::isSubmit('product_availability_check_by_zipcode')) {
                $formvalue = Tools::getvalue('product_availability_check_by_zipcode');
                $error_count = 0;
                /*Knowband validation start*/
                if (!empty($formvalue['zone_name'])) {
                    $sql0 = 'SELECT zone_name FROM `'._DB_PREFIX_.'kb_pacbz_zones`' .
                        ' where zone_name ="' .pSQL($formvalue['zone_name']).'" AND id_shop=' . Context::getContext()->shop->id;
                    $results0 = Db::getInstance()->ExecuteS($sql0);
                    if ($results0 != null) {
                        $this->errors[] = $module->l('This zone name already exist.', 'AdminGlobalZones');
                        $error_count++;
                    }
                } else {
                    $this->errors[] = $module->l('Please enter zone name.', 'AdminGlobalZones');
                    $error_count++;
                }
                /*Knowband validation end*/
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
                                $this->errors[] = $module->l('Please enter valid zip-code.', 'AdminGlobalZones');
                                $error_count++;
                                break;
                            }

                            $sql1 = 'SELECT m.zipcode,z.zone_name FROM `'._DB_PREFIX_ .
                                'kb_pacbz_zone_zipcode_mapping`' .
                                ' as m inner join `'._DB_PREFIX_.'kb_pacbz_zones` as z on' .
                                ' z.id_kb_pacbz_zones = m.zone_id where zipcode ="' .pSQL($pv_zip_code).'" and z.is_seller_zone = "0"';
                            $results = Db::getInstance()->ExecuteS($sql1);
                            if ($results != null) {
                                foreach ($results as $result) {
                                    $erro1 = $module->l('Zipcode ', 'AdminGlobalZones');
                                    $erro2 = $module->l(' already exist in zone', 'AdminGlobalZones');
                                    $this->errors[] = $erro1." ".$result['zipcode']." ". $erro2 ." ". $result['zone_name'];
                                    $error_count++;
                                    break;
                                }
                            }
                        } else {
                            $this->errors[] = $module->l('Please enter valid zip-code.', 'AdminGlobalZones');
                            $error_count++;
                            break;
                        }
                        $i++;
                    }
                } else {
                    $this->errors[] = $module->l('Please enter at least one zip-code.', 'AdminGlobalZones');
                    $error_count++;
                }
                /*Knowband validation end*/
                /*Knowband validation start*/
                $formvalue['deliver_by'] = trim($formvalue['deliver_by']);
                if ($formvalue['deliver_by'] != null) {
                    if (preg_match('/^\d+$/', $formvalue['deliver_by']) && $formvalue['deliver_by'] > 0) {
                        if (Tools::strlen((string)$formvalue['deliver_by']) > 3) {
                            $this->errors[] = $module->l('Number of days must be less than 1000.', 'AdminGlobalZones');
                            $error_count++;
                        }
                    } else {
                        $this->errors[] = $module->l('Only positive integer values allowed.', 'AdminGlobalZones');
                        $error_count++;
                    }
                } else {
                    $this->errors[] = $module->l('Please enter number of days of delivery.', 'AdminGlobalZones');
                    $error_count++;
                }
            /*Knowband validation end*/
                if ($error_count == 0) {
                    //Inserting values if no error occurs
                    $sql2 = 'INSERT INTO `'._DB_PREFIX_.'kb_pacbz_zones`(`id_kb_pacbz_zones`, `zone_name`, `id_shop`) ' .
                            'VALUES ("" , "'.pSQL($formvalue['zone_name']).'" , ' . Context::getContext()->shop->id .')';
                    Db::getInstance()->execute($sql2);

                    $zone_id = Db::getInstance()->Insert_ID();
                    
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
                                 
                    Tools::redirectAdmin('index.php?controller=AdminGlobalZones&success_add=1&token='.$this->token);
                }
            }
        } elseif (isset($_REQUEST['updatekb_pacbz_zones'])) {
            /**
             * Below code is used to validating and saving the data for "Edit zone" page, and is triggered whenwver we click the save button on the "Edit zone" page
             * @date 14-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
            //Perform validation when Edit Zone form is submitted
            /*Knowband validation start*/
            if (Tools::isSubmit('product_availability_check_by_zipcode')) {
                $formvalue = Tools::getvalue('product_availability_check_by_zipcode');
                $error_count = 0;
                if (!empty($formvalue['zone_name'])) {
                    $sql0 = 'SELECT zone_name FROM `'._DB_PREFIX_.'kb_pacbz_zones`' .
                        ' where zone_name ="' .pSQL($formvalue['zone_name']).'" AND id_shop=' . Context::getContext()->shop->id;
                    $results0 = Db::getInstance()->ExecuteS($sql0);
                    if ($results0 != null) {
                        $this->errors[] = $module->l('This zone name already exist.', 'AdminGlobalZones');
                        $error_count++;
                    }
                } else {
                    $this->errors[] = $module->l('Please enter zone name.', 'AdminGlobalZones');
                    $error_count++;
                }
                /*Knowband validation end*/
                if ($error_count == 0) {
                    //Inserting values if no error occurs
                    $sql4 = 'UPDATE `'._DB_PREFIX_.'kb_pacbz_zones` SET zone_name = "'.pSQL($formvalue['zone_name']).
                        '" where id_kb_pacbz_zones ="' .pSQL($formvalue['zone_id']).'"';
                    Db::getInstance()->execute($sql4);
                    Tools::redirectAdmin('index.php?controller=AdminGlobalZones&success_edit=1&token='.$this->token);
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
        
        foreach ($csv_file_array as $pv_zip_code) {
            if (!($i == 0 || $i == count($csv_file_array)-1)) {
                $pv_zip_code = array_map('trim', $pv_zip_code);
                
                $sql = 'SELECT * FROM `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping`' .
                        ' where zipcode ="' .pSQL($pv_zip_code[0]).'"';
                $results = Db::getInstance()->ExecuteS($sql);
                
                if ($results == null) {
                    $sql2 = 'INSERT INTO `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping`(' .
                        '`id_kb_pacbz_zone_zipcode_mapping`, `zone_id`, `zipcode`' .
                        ',`deliver_by` ,`availability`) VALUES ("" ,"'.pSQL($zone_id).'", "' .
                        pSQL($pv_zip_code[0]).'" , "'.pSQL($pv_zip_code[1]).'" , "'.pSQL($pv_zip_code[2]).'")';
                    Db::getInstance()->execute($sql2);
                } else {
                    // Update the value if admin already added that zipcode
                    $sql = 'UPDATE `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` SET ' .
                        'deliver_by = "'.pSQL($pv_zip_code[1]).'" , availability="'.pSQL($pv_zip_code[2]) .
                        '"  WHERE zipcode = "'.pSQL($pv_zip_code[0]).'"';
                    Db::getInstance()->execute($sql);
                }
            }
            $i++;
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
        $avoid_first_row = 0;
        $false_flag = 0;
        foreach ($csv_file_array as $formvalue) {
            if (count($csv_file_array)-1 > $avoid_first_row) {
                if ($avoid_first_row != 0) {
                    if (count($formvalue) <= 3) {
                        $formvalue = array_map('trim', $formvalue);
                        
                        if ($formvalue[0] != null) {
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

                        if ($formvalue[1] != null) {
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

                        if ($formvalue[2] != null) {
                            if (!($formvalue[2] == 1 || $formvalue[2] == 0)) {
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
    
    /**
     * Function responsible for converting the imported CSV data to the array format for further operations.
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return array CSV data array
     */
    public function readCSVtoArray($csv_file)
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to read data from csv file
        if (Tools::getShopProtocol() == 'https://') {
            $file_path = _PS_BASE_URL_SSL_._MODULE_DIR_.'productavailabilitycheckbyzipcode/views/file/';
        } else {
            $file_path = _PS_BASE_URL_._MODULE_DIR_.'productavailabilitycheckbyzipcode/views/file/';
        }
        
        $csvData = Tools::file_get_contents($file_path.$csv_file);
        $lines = explode("\n", $csvData);
        $array = array();
        foreach ($lines as $line) {
            $array[] = str_getcsv($line);
        }
        return $array;
    }
    
    /**
     * Function responsible to delete the existing zones, triggered when we click the delete button on the "GLobal Zone" page
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function processDelete()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to delete zone
        $zone_id = Tools::getValue('id_kb_pacbz_zones');
        if (isset($_REQUEST['deletekb_pacbz_zones'])) {
            $sql = 'DELETE FROM `'._DB_PREFIX_.'kb_pacbz_zones` WHERE' .
                ' id_kb_pacbz_zones = "'.pSQL($zone_id).'" ';
            Db::getInstance()->execute($sql);
            
            $sql1 = 'DELETE FROM `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` WHERE' .
                ' zone_id = "'.pSQL($zone_id).'" ';
            Db::getInstance()->execute($sql1);
            Tools::redirectAdmin('index.php?controller=AdminGlobalZones&success_delete=1&token='.$this->token);
        }
    }
    
    /**
     * Function responsible to delete the bulk zones
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function processBulkDelete()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to delete zone in bulk
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $zone_id) {
                $sql = 'DELETE FROM `'._DB_PREFIX_.'kb_pacbz_zones` WHERE' .
                    ' id_kb_pacbz_zones = "'.pSQL($zone_id).'" ';
                Db::getInstance()->execute($sql);

                $sql1 = 'DELETE FROM `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` WHERE' .
                    ' zone_id = "'.pSQL($zone_id).'" ';
                Db::getInstance()->execute($sql1);
            }
        }
    }
    
    public function renderErrorTranslationsTemplate()
    {
        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'productavailabilitycheckbyzipcode/views/templates/admin/_configure/helpers/form/gloal_zone_variables.tpl'
        );
    }
}
