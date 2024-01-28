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

include_once(_PS_MODULE_DIR_.'productavailabilitycheckbyzipcode/classes/ProductMap.php');
class AdminProductMapController extends ModuleAdminControllerCore
{
    /**
     * Function responsible to Creating list of total available products for each zones
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function __construct()
    {
        //Redirecting visitor to AdminProductDetails Controller when view is clicked
        if (Tools::getValue('id_kb_pacbz_zones') != null && isset($_REQUEST['viewkb_pacbz_zones'])) {
            $linkobj = new Link();
            $red_path = $linkobj->getAdminLink('AdminProductDetails').'&id_kb_pacbz_zones=' .
                Tools::getValue('id_kb_pacbz_zones');
            Tools::redirectAdmin($red_path);
        }
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Creating Zone List
        $this->table = 'kb_pacbz_zones';
        $this->className = 'ProductMap';
        $this->show_toolbar = true;
        $this->context = Context::getContext();
        $this->list_no_link = true;
        $this->csv_line_number = array();
        $this->csv_invalid = 0;
        $this->bootstrap = true;
        $this->_select = 'a.`id_kb_pacbz_zones` as zid,a.`zone_name` as zname,count(*) as product_count';
        $this->_join = 'INNER JOIN `' . _DB_PREFIX_ . 'kb_pacbz_product_zone_mapping` z ON ' .
            'a.id_kb_pacbz_zones = z.zone_id';
        $this->_where .= ' AND a.is_seller_zone = "0" AND id_shop=' . Context::getContext()->shop->id;
        $this->_where .= ' AND a.is_seller_zone = "0"';
        $this->_group = ' GROUP BY z.zone_id';
        
        parent::__construct();

        $this->fields_list = array(
            'id_kb_pacbz_zones' => array(
                'title' => $module->l('Zone ID', 'AdminProductMap'),
                'align' => 'center',
                'class' => 'fixed-width-lg',
                  
            ),
            'zone_name' => array(
                'title' => $module->l('Zone Name', 'AdminProductMap'),
                'align' => 'center',
            ),
            'product_count' => array(
                'title' => $module->l('Total Products', 'AdminProductMap'),
                'align' => 'center',
                'havingFilter' => true
            ),
        );

        //Display success messages
        if (Tools::getValue('success_add') == 1) {
            $this->confirmations = $module->l('Product succesfully added.', 'AdminProductMap');
        }

        if (Tools::getValue('success_delete') == 1) {
            $this->confirmations = $module->l('Zone-Product Mapping succesfully deleted.', 'AdminProductMap');
        }
        
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $module->l('Delete selected', 'AdminProductMap'),
                'confirm' => $module->l('Delete selected items?', 'AdminProductMap'),
                'icon' => 'icon-trash'
            )
        );
    }
    
    /**
     * Function responsible to Add the view and Delete button for the list generated from the construct function.
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function renderList()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        // Adds a View button for each result
        $this->addRowAction('view');
        // Adds a Delete button for each result
        $this->addRowAction('delete');
  
        return parent::renderList();
    }

    /**
     * Function responsible to Add the "Add new" button on the "Product-Zone Mapping" admin page
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function initPageHeaderToolbar()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to display add button in same controller
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['addkb_pacbz_zones'] = array(
                'href' => self::$currentIndex . '&addkb_pacbz_zones&token=' . $this->token,
                //'desc' => $module->l('Add new', null, null, false),
                'desc' => $module->l('Add new', 'AdminProductMap'),
                'icon' => 'process-icon-new'
            );
        }
        parent::initPageHeaderToolbar();
    }
    
    /**
     * Function responsible to Add the "Back" button on the "Product-Zone Mapping" admin page
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function initPageHeaderToolbarNew()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to display back button in same controller
        $this->page_header_toolbar_btn['backkb_pacbz_zone_zipcode_mapping'] = array(
            'href' => 'index.php?controller=AdminProductMap&token=' .
                Tools::getAdminTokenLite('AdminProductMap'),
            'desc' => $module->l('Back', 'AdminProductMap'),
            'icon' => 'process-icon-back'
        );
        parent::initPageHeaderToolbar();
    }
    
    /**
     * Function responsible for Creating configuration setting form for "Add New Product-Zone Mapping" page
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function renderForm()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Display form in Add page
        $this->initPageHeaderToolbarNew();
        if (Tools::getShopProtocol() == 'https://') {
            $av_protocol_name = _PS_BASE_URL_SSL_ . _MODULE_DIR_;
        } else {
            $av_protocol_name = _PS_BASE_URL_ . _MODULE_DIR_;
        }
        
          /**
         * load autocomplete JS 
         * @date 03-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        $this->context->smarty->assign('kb_admin_link', $this->context->link->getAdminLink('AdminModules', true).'&configure=productavailabilitycheckbyzipcode&ajaxproductaction=true');
        $this->context->controller->addJs($av_protocol_name . 'productavailabilitycheckbyzipcode/views/js/admin/jquery.autocomplete.js');
        $this->context->controller->addJs($av_protocol_name . 'productavailabilitycheckbyzipcode/views/js/admin/product_availability_check_by_zipcode_admin.js');
        
        
        $this->context->controller->addJS($av_protocol_name . 'productavailabilitycheckbyzipcode/views/js/admin/zonevalidation.js');
        $oss_admin_js = 'productavailabilitycheckbyzipcode/views/js/admin/kb_add_product.js';
        $oss_admin_js2 = 'productavailabilitycheckbyzipcode/views/js/admin/select2.min.js';
        $oss_admin_js3 = 'productavailabilitycheckbyzipcode/views/js/admin/select2.js';
        $oss_admin_css = 'productavailabilitycheckbyzipcode/views/css/admin/select2.css';
        $this->context->controller->addJS($av_protocol_name.$oss_admin_js);
        $this->context->controller->addJS($av_protocol_name.$oss_admin_js2);
        $this->context->controller->addJS($av_protocol_name.$oss_admin_js3);
        $this->context->controller->addCSS($av_protocol_name.$oss_admin_css);

        $formvalue = array();
        $languages = Language::getLanguages(true);
        $config = Configuration::get('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE_ADD_PRODUCT');
        $formvalue = json_decode($config, true);
        
        if (Tools::isSubmit('product_availability_check_by_zipcode')) {
            $formvalue = Tools::getvalue('product_availability_check_by_zipcode');
            $product_zipcode_array = Tools::getvalue('product_availability_check_by_zipcode');
            $formvalue['selected_file'] = $product_zipcode_array['selected_file'];
            if (!isset($formvalue['select_zone'])) {
                $formvalue['select_zone'] = '';
            }
            
              /**
             * fetch the data from "kb_home_specific_product_items" and assign the same to "search_product" for further calculation
             * @date 03-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
             if(!empty(Tools::getValue('kb_home_specific_product_items'))){
                $formvalue['search_product'] = explode(',', Tools::getValue('kb_home_specific_product_items'));
            }
            
            if (!isset($formvalue['search_product'])) {
                $formvalue['search_product'] = '';
            }
            
        }

        $upload_csv = array(
            'label' => $module->l('Upload CSV File', 'AdminProductMap'),
            'type' => 'switch',
            'hint' => $module->l('Upload CSV File to add large number of zip-codes.', 'AdminProductMap'),
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

        $sql_zone = 'SELECT * FROM `'._DB_PREFIX_.'kb_pacbz_zones` where is_seller_zone = 0 AND id_shop=' . Context::getContext()->shop->id;
        $display_options = array();
        if ($results = Db::getInstance()->ExecuteS($sql_zone)) {
            $i = 0;
            $display_options = array();
            foreach ($results as $row) {
                $display_options[$i] = array(
                    'id_option' => $row['id_kb_pacbz_zones'],
                    'name' => $row['zone_name']
                );
                $i++;
            }
        }

        /**
         * Commented below code, as earlier we use the select functioanlity for the "Disable product", but now we are using autocomplete functioanlity.
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
//        $sql8 = 'SELECT l.name,l.id_product,p.reference FROM `'
//            ._DB_PREFIX_.'product_lang` as l inner join `'._DB_PREFIX_.'product` as p' .
//            ' on l.id_product = p.id_product group by l.id_product';
//
//        $product_options = Db::getInstance()->ExecuteS($sql8);
//
//        $i = 0;
//        $option1 = array();
//        foreach ($product_options as $product_options) {
//            $option1[$i]['id_module'] = $product_options['id_product'];
//            $option1[$i]['name'] = $product_options['name'].' : '.$product_options['reference'];
//            $i++ ;
//        }
        
        /**
         * Below code is responsible to fetch the disabled products saved in DB, and then assign the same to TPL
         * @date 03-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        if (isset($formvalue['product_availability_check_by_zipcode']['upload_csv'])) {
            $search_product = $formvalue['product_availability_check_by_zipcode']['search_product'];
            if (empty($search_product)) {
                $kb_home_page_specific_product_data = "";
            } else {
                $kb_home_page_specific_product_data = implode(',', $search_product);
            }
        } else {
            if (empty($formvalue['search_product'])) {
                $kb_home_page_specific_product_data = "";
            } else {
                $kb_home_page_specific_product_data = implode(',', $formvalue['search_product']);
            }
        }

        $selectedhomeproducts = array();
        if (isset($kb_home_page_specific_product_data) && (!empty($kb_home_page_specific_product_data))) {
            $selectedhomeProductIds = explode(',', $kb_home_page_specific_product_data);
            foreach ($selectedhomeProductIds as $productId) {
                $productDetails = new Product($productId);
                $selectedhomeproducts[] = array(
                    'product_id' => $productId,
                    'title' => $productDetails->name[$this->context->language->id],
                    'reference' => $productDetails->reference);
            }
        }
        $this->context->smarty->assign('selectedhomeproducts', $selectedhomeproducts);
        
        $this->fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $module->l('Add new', 'AdminProductMap'),
                ),
                'input' => array(
                    $upload_csv,
//                    array(
//                        'type' => 'select',
//                        'label' => $module->l('Search Product', 'AdminProductMap'),
//                        'name' => 'product_availability_check_by_zipcode[search_product][]',
//                        'multiple' => 'multiple',
//                        'hint' => $module->l('Select products to map with zone', 'AdminProductMap'),
//                        'is_bool' => true,
//                        'required' => true,
//                        'options' => array(
//                            'query'=> $option1,
//                            'id' =>  'id_module',
//                            'name'=>  'name'
//                        )
//                    ),
                    /**
                     * Below code is responsible to Displaying the search product Field on the Admin controller file
                     * @date 03-02-2023
                     * @author 
                     * @commenter Vishal Goyal
                     */
                    array(
                        'type' => 'text',
                        'label' => $module->l('Search Product', 'AdminProductMap'), // The <label> for this <select> tag.
                        'hint' => $module->l('Select products to map with zone', 'AdminProductMap'),
                        'name' => 'product_availability_check_by_zipcode_disabled', // The content of the 'id' attribute of the <select> tag.
                    ),
                    array(
                        'type' => 'html',
                        'name' => '',
                        'html_content' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'productavailabilitycheckbyzipcode/views/templates/admin/showSelectedProducts_home_page.tpl'),
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'kb_home_specific_product_items',
                    ),
                    
                    array(
                        'type' => 'select',
                        'multiple' =>true,
                        'label' => $module->l('Select Zone', 'AdminProductMap'),
                        'name' => 'product_availability_check_by_zipcode[select_zone][]',
                        'hint' => $module->l('Enter define the zone name.', 'AdminProductMap'),
                        'class' => 'optn_general test123',
                        'required' => true,
                        'maxlength' => 120,
                        'cols' => 100,
                        'options' => array(
                            'query' => $display_options,
                            'id' => 'id_option',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'file',
                        'label' => $module->l('Choose CSV File', 'AdminProductMap'),
                        'name' => 'product_availability_check_by_zipcode[selected_file]',
                        'id' => 'uploadedfile',
                        'required' => true,
                        'display_image' => true,
                        'desc' => $module->l('Download Sample of CSV File', 'AdminProductMap'),
                        'hint' => $module->l('Select the CSV File to be add zipcodes.', 'AdminProductMap'),
                    ),
                ),
                'submit' => array(
                    'title' => $module->l('Save', 'AdminProductMap'),
                    'class' => 'btn btn-default pull-right product_map_add'
                ),
            ),
        );
        
        if (version_compare(_PS_VERSION_, '1.6.0.7', '>')) {
            $this->fields_form['form']['input'][1]['id'] = 'multiple-select';
        } else {
            $this->fields_form['form']['input'][1]['id'] = 'multiple-select-chosen';
        }
        
        if (isset($formvalue['product_availability_check_by_zipcode']['upload_csv'])) {
            $de_upload_csv = $formvalue['product_availability_check_by_zipcode']['upload_csv'];
            $search_product = $formvalue['product_availability_check_by_zipcode']['search_product'];
            $zone_name = $formvalue['product_availability_check_by_zipcode']['select_zone'];
            $selected_file = $formvalue['product_availability_check_by_zipcode']['selected_file'];
            
            $field_value = array(
                'product_availability_check_by_zipcode[upload_csv]' => $de_upload_csv,
                'product_availability_check_by_zipcode[select_zone][]' => $zone_name,
                'product_availability_check_by_zipcode[selected_file]' => $selected_file,
                'product_availability_check_by_zipcode[search_product][]' => $search_product,
            );
        } else {
            $field_value = array(
                'product_availability_check_by_zipcode[upload_csv]' => $formvalue['upload_csv'],
                'product_availability_check_by_zipcode[select_zone][]' => $formvalue['select_zone'],
                'product_availability_check_by_zipcode[search_product][]' => $formvalue['search_product'],
                'product_availability_check_by_zipcode[selected_file]' => $formvalue['selected_file'],
            );
        }
        //vishal
        $field_value['product_availability_check_by_zipcode_disabled'] = "";
        if (!empty($field_value['product_availability_check_by_zipcode[search_product][]'])) {
            $field_value['kb_home_specific_product_items'] = implode(',', $field_value['product_availability_check_by_zipcode[search_product][]']);
        } else {
            $field_value['kb_home_specific_product_items'] = "";
        }

        $helper = new HelperForm();
        $helper->fields_value = $field_value;
        $languages = Language::getlanguages(true);
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
        }

        $action = 'index.php?controller=AdminProductMap&addkb_pacbz_zones&token='.$this->token;
        $helper->name_controller = $this->controller_name;
        $helper->languages = $languages;
        $helper->default_form_language = $this->context->language->id;
        
        $path_current_index = 'index.php?controller=AdminProductMap&addkb_pacbz_zones&token=';
        $helper->currentIndex = $path_current_index.Tools::getAdminTokenLite('AdminProductMap');
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->show_cancel_button = false;
        $helper->submit_action = $action;
        $helper->no_link = true;
        $form = $helper->generateForm(array($this->fields_form));

        $views_path = 'productavailabilitycheckbyzipcode/views/';
        $file_name = 'file/document_product_zone.csv';
        if (Tools::getShopProtocol() == 'https://') {
            $download_path = _PS_BASE_URL_SSL_ . _MODULE_DIR_ . $views_path .$file_name;
        } else {
            $download_path = _PS_BASE_URL_ . _MODULE_DIR_ . $views_path .$file_name;
        }
        
        $linkobj = new Link();
        $kb_product_link = $linkobj->getAdminLink('AdminProducts');
        /**
        * Change made to the redirect URl to avoid the error of Invalid token 
        * added token to along with URL  
        * 
        * ASJan2024 token_added_url
        * @date 03-01-2024
        * @modifier Amit Singh
        */
        $kb_zone_link = 'index.php?controller=AdminGlobalZones&token='.Tools::getAdminTokenLite('AdminGlobalZones');
        // End of Change

        $this->context->smarty->assign('download_path', $download_path);
        $this->context->smarty->assign('display_export_link', 1);
        $this->context->smarty->assign('product_link', $kb_product_link);
        $this->context->smarty->assign('zone_link', $kb_zone_link);
        $content = $this->context->smarty->fetch(_PS_MODULE_DIR_. $views_path. 'templates/admin/order_setting.tpl');

        return $form.$content;
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
        //Function to perform validation when submitted
        /**
         * Code responsible for Validating the "Add new" page form Data
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        if (Tools::isSubmit('product_availability_check_by_zipcode')) {
            $formvalue = Tools::getvalue('product_availability_check_by_zipcode');
             if(!empty(Tools::getValue('kb_home_specific_product_items'))){
                $formvalue['search_product'] = explode(',', Tools::getValue('kb_home_specific_product_items'));
            }
            
            $error_count = 0;
            /*Knowband validation start*/
            if ($formvalue['upload_csv'] == 0) {
                if (!isset($formvalue['search_product'])) {
                    $this->errors[] = $module->l('Please search product to map with zone.', 'AdminProductMap');
                    $error_count++;
                }

                if (!isset($formvalue['select_zone'])) {
                    $this->errors[] = $module->l('Please select zone to map with the product.', 'AdminProductMap');
                    $error_count++;
                }
            } else {
                $product_zipcode_array = Tools::getvalue('product_availability_check_by_zipcode');
                $formvalue['selected_file'] = $product_zipcode_array['selected_file'];
                if ($formvalue['selected_file'] == null) {
                    $this->errors[] .= $module->l('Please upload the CSV file.', 'AdminProductMap');
                    $error_count++;
                } else {
                    if ($_FILES['product_availability_check_by_zipcode']['name']['selected_file'] != null) {
                        $file_name = $_FILES['product_availability_check_by_zipcode']['name']['selected_file'];
                        $file_size = $_FILES['product_availability_check_by_zipcode']['size']['selected_file'];
                        $file_tmp = $_FILES['product_availability_check_by_zipcode']['tmp_name']['selected_file'];

                        $x=explode('.', $file_name);
                        $file_ext = Tools::strtolower(end($x));
                        $expensions = array("csv");
                        $formvalue['selected_file'] = "sample_product_zone_mapping.".$file_ext;

                        
                        if (in_array($file_ext, $expensions) === false) {
                            $this->errors[] .= $module->l('Please choose file with .csv extension.', 'AdminProductMap');
                            $error_count++;
                        }

                        if ($file_size >= 4194304) {
                            $this->errors[] .= $module->l('File size must be less than 4 MB.', 'AdminProductMap');
                            $error_count++;
                        }
                        
                        if ($error_count == 0) {
                            if ($_FILES['product_availability_check_by_zipcode']['name']['selected_file'] != null) {
                                $file_path1 = 'productavailabilitycheckbyzipcode/views/file/' ;
                                move_uploaded_file(
                                    $file_tmp,
                                    _PS_MODULE_DIR_.$file_path1."sample_product_zone_mapping.".$file_ext
                                );
                                chmod(_PS_MODULE_DIR_ . $file_path1. 'sample_product_zone_mapping.'.$file_ext, 0755);
                            }

//                            $csv_file_array = $this->readCSVtoArray("sample_product_zone_mapping.".$file_ext);
                            
                            $path = _PS_MODULE_DIR_ . $file_path1 . "sample_product_zone_mapping." . $file_ext;
                            $csv_file_array = $this->readCSVtoArray($path, ',');

                            if ($this->validateCSVFile($csv_file_array) == false) {
                                // In case of incorrect data csv data
                                if ($this->csv_invalid == 1) {
                                    $this->errors[] .= $this->l('Invalid content inside CSV file.Please check the sample document.');
                                } else {
                                    $pv_part1 = $module->l('Please check ', 'AdminProductMap');
                                    $pv_part2 = $module->l('th row of CSV File.Following are the validation rules-', 'AdminProductMap');
                                    $pv_middle = implode(",", array_unique($this->csv_line_number));
                                    $this->errors[] .= $pv_part1 ." ". $pv_middle . $pv_part2;
                                    
                                    $product_table1 = $module->l('Product Id must exist.', 'AdminProductMap');
                                                                   
                                    $zone_table1 = $module->l('Zone Id must exist.', 'AdminProductMap');
                                    
                                    $this->errors[] .= $product_table1;
                                    $this->errors[] .= $zone_table1;
                                    
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
             * Code responsible for Saving the "Add new" page form in DB if no error found
             * @date 14-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
            if ($error_count == 0) {
                //Inserting values in DB if no error occurs
                /**
                 * Code responsible for Saving the "Add new" page form in DB if "Upload CSV File" is disabled
                 * @date 14-02-2023
                 * @author 
                 * @commenter Vishal Goyal
                 */
                if ($formvalue['upload_csv'] == 0) {
                    foreach ($formvalue['search_product'] as $productid) {
                        foreach ($formvalue['select_zone'] as $zoneid) {
                            $sql0 = 'SELECT * FROM `'._DB_PREFIX_.'kb_pacbz_product_zone_mapping`' .
                                ' where product_id ="' .pSQL($productid).'" and zone_id = "'.pSQL($zoneid).'"';
                            $results0 = Db::getInstance()->ExecuteS($sql0);
                            if ($results0 == null) {
                                $sql4 = 'INSERT INTO `'._DB_PREFIX_.'kb_pacbz_product_zone_mapping` ' .
                                    '(`product_id`, `zone_id`) VALUES ("'.
                                    pSQL($productid).'" , "'.pSQL($zoneid).'")';
                                Db::getInstance()->execute($sql4);
                                
                                $sql8 = 'SELECT * FROM `'._DB_PREFIX_.'kb_pacbz_products`' .
                                    ' where id_kb_pacbz_products ="' .pSQL($productid).'"';
                                $results1 = Db::getInstance()->ExecuteS($sql8);
                                if ($results1 == null) {
                                    $sql8 = 'SELECT l.name,l.id_product,p.reference FROM `'
                                        ._DB_PREFIX_.'product_lang` as l inner join `'._DB_PREFIX_.'product` as p' .
                                        ' on l.id_product=p.id_product where l.id_product ="'
                                        .pSQL($productid).'" group by l.id_product';
                                    $results2 = Db::getInstance()->ExecuteS($sql8);
                                    
                                    foreach ($results2 as $result) {
                                        $sql9 = 'INSERT INTO `'._DB_PREFIX_.'kb_pacbz_products` ( ' .
                                        '`id_kb_pacbz_products`,`product_name`) VALUES ("'
                                        .pSQL($result['id_product']).'","'.pSQL($result['name']).' : '
                                        .pSQL($result['reference']).'")';
                                        Db::getInstance()->execute($sql9);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    /**
                     * Code responsible for Saving the "Add new" page form in DB if "Upload CSV File" is enabled and we have uploaded the CSV file
                     * @date 14-02-2023
                     * @author 
                     * @commenter Vishal Goyal
                     */
                    $this->insertCsvData($csv_file_array);
                }
                $linkobj = new Link();
                Tools::redirectAdmin($linkobj->getAdminLink('AdminProductMap'));
            }
        }
        parent::initProcess();
    }
    
    /**
     * Function responsible to Adding the CSV file data in the "kb_pacbz_products" table.
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function insertCsvData($csv_file_array)
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to insert data from csv file
        $i = 0;
        unset($csv_file_array[0]);
        foreach ($csv_file_array as $values) {
            /**
             * Added the condition to check that the 'values' array is not empty.
             * TGsep2023 Empty-Condition
             * @date 27-09-2023
             * @author Tanisha Gupta
             */
            if(!empty($values)){
                $sql0 = 'SELECT * FROM `' . _DB_PREFIX_ . 'kb_pacbz_product_zone_mapping`' .
                        ' where product_id ="' . pSQL($values[0]) . '" and zone_id = "' . pSQL($values[1]) . '"';
                $results0 = Db::getInstance()->ExecuteS($sql0);
                if ($results0 == null) {
                    $sql4 = 'INSERT INTO `' . _DB_PREFIX_ . 'kb_pacbz_product_zone_mapping` ' .
                            '(`product_id`, `zone_id`) VALUES ("' . pSQL($values[0]) . '" , "' . pSQL($values[1]) . '")';
                    Db::getInstance()->execute($sql4);

                    $sql8 = 'SELECT * FROM `' . _DB_PREFIX_ . 'kb_pacbz_products`' .
                            ' where id_kb_pacbz_products ="' . pSQL($values[0]) . '"';
                    $results1 = Db::getInstance()->ExecuteS($sql8);
                    if ($results1 == null) {
                        $sql8 = 'SELECT l.name,l.id_product,p.reference FROM `'
                                . _DB_PREFIX_ . 'product_lang` as l inner join `' . _DB_PREFIX_ . 'product` as p' .
                                ' on l.id_product=p.id_product where l.id_product ="'
                                . pSQL($values[0]) . '" group by l.id_product';
                        $results2 = Db::getInstance()->ExecuteS($sql8);

                        foreach ($results2 as $result) {
                            $sql9 = 'INSERT INTO `' . _DB_PREFIX_ . 'kb_pacbz_products` ( ' .
                                    '`id_kb_pacbz_products`,`product_name`) VALUES ("' . pSQL($result['id_product']) .
                                    '","' . pSQL($result['name']) . ' : ' . pSQL($result['reference']) . '")';
                            Db::getInstance()->execute($sql9);
                            break;
                        }
                    }
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
        $avoid_first_row = 0;
        $false_flag = 0;
        foreach ($csv_file_array as $formvalue) {
            if (count($csv_file_array)-1 > $avoid_first_row) {
                if ($avoid_first_row != 0) {
                    if (count($formvalue) <= 2) {
                        $formvalue = array_map('trim', $formvalue);
                        
                        if ($formvalue[0] != null) {
                            $product_query = 'SELECT id_product FROM ';
                            $product_query .= _DB_PREFIX_.'product WHERE id_product="'.pSQL($formvalue[0]).'"';
                            if (!Db::getInstance()->getValue($product_query)) {
                                $this->csv_line_number[] .= $avoid_first_row +1;
                                $false_flag = 1;
                            }
                        } else {
                            $this->csv_line_number[] .= $avoid_first_row +1;
                            $false_flag = 1;
                        }

                        if ($formvalue[1] != null) {
                            $zone_query = 'SELECT id_kb_pacbz_zones FROM '._DB_PREFIX_.'kb_pacbz_zones' .
                                ' WHERE id_kb_pacbz_zones="'.pSQL($formvalue[1]).'"';
                            
                            if (!Db::getInstance()->getValue($zone_query)) {
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
     * Function responsible to delete the existing product-zone Mapping, triggered when we click the delete button on the "Product-Zone Mapping" page. Note : Here we will delete the all the products mapped with specific zone
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function processDelete()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to delete Product mapped with a Zone
        $product_zone_map_id = Tools::getValue('id_kb_pacbz_zones');
        if (isset($_REQUEST['deletekb_pacbz_zones'])) {
            $sql1 = 'DELETE FROM `'._DB_PREFIX_.'kb_pacbz_product_zone_mapping` WHERE' .
                ' zone_id = "'.pSQL($product_zone_map_id).'" ';
            Db::getInstance()->execute($sql1);
            Tools::redirectAdmin('index.php?controller=AdminProductMap&success_delete=1&token='.$this->token);
        }
    }
    
    /**
     * Function responsible to delete the bulk product-zone Mapping.
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function processBulkDelete()
    {
        $module = Module::getInstanceByName('productavailabilitycheckbyzipcode');
        //Function to delete Product in bulk that are mapped with a Zone
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $product_zone_map_id) {
                $sql1 = 'DELETE FROM `'._DB_PREFIX_.'kb_pacbz_product_zone_mapping` WHERE' .
                ' zone_id = "'.pSQL($product_zone_map_id).'" ';
                Db::getInstance()->execute($sql1);
            }
            Tools::redirectAdmin('index.php?controller=AdminProductMap&success_delete=1&token='.$this->token);
        }
    }
}
