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

class ProductAvailabilityCheckByZipcode extends Module
{
    public function __construct()
    {
        $this->name = 'productavailabilitycheckbyzipcode';
        $this->tab = 'front_office_features';
        $this->version = '3.0.0';
        $this->author = 'Knowband';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '40ec1221ff53e3ad746fe74ddb80bd03';
        $this->author_address = '0x2C366b113bd378672D4Ee91B75dC727E857A54A6';

        parent::__construct();

        $this->displayName = $this->l('Product Availability Check by Zipcode');
        $this->description = $this->l('This module allows customers to check the availability of the product at any region by Zip-code.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->ps_versions_compliancy = array(
           'min' => '1.7',
           'max' => _PS_VERSION_
        );
        if (!Configuration::get('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE')) {
            $this->warning = $this->l('No name provided');
        }
    }

    /**
     * Function responsible to install the module , set the default settings of the module.
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return bool
     */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
            !$this->registerHook('displayBackOfficeHeader') ||
            !$this->registerHook('displayHeader') ||
            !$this->registerHook('displayBeforeCarrier') ||
            /*
             * Added by tarun for popup link
             */
            !$this->registerHook('displayNav1') ||
            //changes over
            !$this->registerHook('displayFooterProduct')) {
            return false;
        }
        
        /**
         * Create the Tables - zones , products , zone-zipcode map , product-zone map for our module
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        $pacbz_zones_table = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'kb_pacbz_zones` (
            `id_kb_pacbz_zones` int(11) NOT NULL AUTO_INCREMENT,
            `zone_name` varchar(250) NOT NULL,
            `is_seller_zone` int(11) DEFAULT "0",
            PRIMARY KEY  (`id_kb_pacbz_zones`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

        $pacbz_zone_zipcode_map_table = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` (
            `id_kb_pacbz_zone_zipcode_mapping` int(11) NOT NULL AUTO_INCREMENT,
            `zone_id` int(11) NOT NULL,
            `zipcode` varchar(20) NOT NULL,
            `deliver_by` int(11) NOT NULL,
            `availability` smallint(2) NOT NULL,
            PRIMARY KEY  (`id_kb_pacbz_zone_zipcode_mapping`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

        $pacbz_products_table = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'kb_pacbz_products` (
            `id_kb_pacbz_products` int(11) NOT NULL,
            `product_name` varchar(250),
            PRIMARY KEY  (`id_kb_pacbz_products`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

        $pacbz_product_zone_map_table = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'kb_pacbz_product_zone_mapping` (
            `id_kb_pacbz_product_zone_mapping` int(11) NOT NULL AUTO_INCREMENT,
            `product_id` int(11) NOT NULL,
            `zone_id` int(11) NOT NULL,
            PRIMARY KEY  (`id_kb_pacbz_product_zone_mapping`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';
        /*
         * Changes by tarun to create a table for zipcode requests
         */
        $zipcodes_request_table = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'kb_zipcodes_requests` (
            `id_request` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
            `zipcode` VARCHAR(255) NOT NULL,
            `controller_name` VARCHAR(255) NOT NULL,
            `count` INT(13) NOT NULL,
            `date_add` DATETIME NOT NULL ,
            `date_upd` DATETIME NULL ,
            PRIMARY KEY (`id_request`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8';
        Db::getInstance()->execute($zipcodes_request_table);
        //Changes over
        Db::getInstance()->execute($pacbz_zones_table);
        Db::getInstance()->execute($pacbz_zone_zipcode_map_table);
        Db::getInstance()->execute($pacbz_products_table);
        Db::getInstance()->execute($pacbz_product_zone_map_table);

        /*Start- MK made changes on 31-05-18 to to add column for privacy policy in seller_lang table*/
        $select_datatype = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'kb_pacbz_zones" AND column_name="is_seller_zone"';
        $data_type = Db::getInstance()->getValue($select_datatype);
        if (empty($data_type)) {
            Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'kb_pacbz_zones ADD COLUMN `is_seller_zone` int(11) DEFAULT "0"');
        }
        /*End- MK made changes on 31-05-18 to to add column for privacy policy in seller_lang table*/
        $select_datatype = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'kb_pacbz_zones" AND column_name="id_shop"';
        $data_type = Db::getInstance()->getValue($select_datatype);
        if (empty($data_type)) {
            Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'kb_pacbz_zones ADD COLUMN `id_shop` int(11) DEFAULT "1"');
        }
        $select_datatype = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'kb_zipcodes_requests" AND column_name="id_shop"';
        $data_type = Db::getInstance()->getValue($select_datatype);
        if (empty($data_type)) {
            Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'kb_zipcodes_requests ADD COLUMN `id_shop` int(11) DEFAULT "1"');
        }
        
        /**
         * Function responsible for creating Admin tab
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = "AdminZipcodeAvailabilityCheck";
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Zipcode Availability');
        }
        $tab->icon = 'location_on';
        $id_tab = Tab::getIdFromClassName("SELL");
        $tab->id_parent = $id_tab;
        $tab->module = $this->name;
        $tab->add();
//        $tab->id_parent = 0;
//        $tab->module = $this->name;
//        $tab->add();

        $id_tab = Tab::getIdFromClassName("AdminZipcodeAvailabilityCheck");
        
        //Adding General Setting Tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = "AdminKbSetting";
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('General Settings');
        }
        $tab->id_parent = $id_tab;
        $tab->module = $this->name;
        $tab->add();
        
        // Adding Global Zones tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = "AdminGlobalZones";
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Global Zones');
        }
        $tab->id_parent = $id_tab;
        $tab->module = $this->name;
        $tab->add();

        // Adding Product-Zone Mapping Controller
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = "AdminProductMap";
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Product-Zone Mapping');
        }
        $tab->id_parent = $id_tab;
        $tab->module = $this->name;
        $tab->add();

        // Adding View Zone(After Global Zone) Controller
        $tab = new Tab();
        $tab->active = 0;
        $tab->class_name = "AdminViewZone";
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('View zone');
        }
        $tab->id_parent = $id_tab;
        $tab->module = $this->name;
        $tab->add();

        // Adding View Zone(After Product-Map list) Controller
        $tab = new Tab();
        $tab->active = 0;
        $tab->class_name = "AdminProductDetails";
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('View zone');
        }
        $tab->id_parent = $id_tab;
        $tab->module = $this->name;
        $tab->add();
        
        //Changes by tarun to add the tab for zipcode requested page list
        // Adding Zipcodes Request tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = "AdminZipcodeRequests";
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l('Knowband All Zipcodes Request');
        }
        $tab->id_parent = $id_tab;
        $tab->module = $this->name;
        $tab->add();
        //changes over
        
        // Assigning default value after installation in Add Zone page and add Product page

        $this->setDefaultAddZone();
        $this->setDefaultAddProduct();

        $defaultsettings = $this->getDefaultSettings();
        Configuration::updateValue('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE', json_encode($defaultsettings));
        return true;
    }
    
    /**
     * Function responsible to Fetching the product list, this function is triggerred whenever the Admin uses the autocomplete functioanlity to fetching the product listing
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return array
     */
    public function ajaxproductlist()
    {
        $query = Tools::getValue('q', false);
        if (!$query or $query == '' or Tools::strlen($query) < 1) {
            die();
        }

        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
         * they are no return values just because string:"(ref : #ref_pattern#)"
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if ($pos = strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $productIds = Tools::getValue('productIds', false);
        if ($productIds && $productIds != 'NaN') {
            $productIds = implode(',', array_map('intval', explode(',', $productIds)));
        } else {
            $productIds = '';
        }

        // Excluding downloadable products from packs because download from pack is not supported
        $excludeVirtuals = (bool) Tools::getValue('excludeVirtuals', false);
        $exclude_packs = (bool) Tools::getValue('exclude_packs', false);

        /**
         * Below Query is used to fetch the product listing on the basis of searched keyword i.e q
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         * @return bool
         */
        $sql = 'SELECT p.`id_product`, `reference`, pl.name
		FROM `' . _DB_PREFIX_ . 'product` p
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = '
            . 'p.id_product AND pl.id_lang = '
            . '' . (int) Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
		WHERE p.active = 1 and (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
            (!empty($productIds) ? ' AND p.id_product NOT IN (' . pSQL($productIds) . ') ' : ' ') .
            (pSQL($excludeVirtuals) ? 'AND p.id_product NOT IN (SELECT pd.id_product FROM '
                . '`' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
            (pSQL($exclude_packs) ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '');

        $items = Db::getInstance()->executeS($sql);
        if ($items) {
            foreach ($items as $item) {
                echo trim($item['name']) . (!empty($item['reference']) ?
                    ' (ref: ' . $item['reference'] . ')' : '') .
                '|' . (int) ($item['id_product']) . "\n";
            }
        }
    }

    /**
     * Function responsible to uninstall the module, delete the tabs
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return array 
     */
    public function uninstall()
    {
        if (!parent::uninstall() ||
            !$this->unregisterHook('displayBackOfficeHeader') ||
            !$this->unregisterHook('displayHeader') ||
            !$this->unregisterHook('displayBeforeCarrier') ||
            //changes by tarun for popup link
            !$this->unregisterHook('displayNav1') ||
            //changes over
            !$this->unregisterHook('displayProductButtons')) {
            return false;
        }

        // Deleting View Zones tab
        $id_tab = Tab::getIdFromClassName('AdminViewZone');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        // Deleting Product Details tab
        $id_tab = Tab::getIdFromClassName('AdminProductDetails');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        // Deleting Global Zones tab
        $id_tab = Tab::getIdFromClassName('AdminGlobalZones');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        // Deleting AdminProductMap tab
        $id_tab = Tab::getIdFromClassName('AdminProductMap');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        // Deleting AdminZipcodeAvailabilityCheck tab
        $id_tab = Tab::getIdFromClassName('AdminZipcodeAvailabilityCheck');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        
        //Deleting Zipciode Request Tab
        $id_tab = Tab::getIdFromClassName('AdminZipcodeRequests');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        return true;
    }

    /**
     * Function responsible to loading all the required CSS and JS on the Admin controller for further usage
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return NULL
     */
    public function hookDisplayBackOfficeHeader($params)
    {
        if (Module::isInstalled('productavailabilitycheckbyzipcode')) {
            //To display tab icon at back-office
            $this->context->controller->addCSS(($this->_path) . 'views/css/admin/menuTabIcon.css');
            if (isset($this->context->controller->module->name) && $this->context->controller->module->name == 'productavailabilitycheckbyzipcode') {
                $this->context->controller->addJS(($this->_path) . 'views/js/admin/velovalidation.js');
            //$this->context->controller->addJS(_PS_MODULE_DIR_ . 'productavailabilitycheckbyzipcode/views/js/admin/zonevalidation.js');
            }
        }
    }

    /**
     * Hook responsible to display the popup for "Set Your Zipcode" on the header 
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return template
     */
    public function hookDisplayHeader($params)
    {
        $values = json_decode(Configuration::get('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE'), true);
        $zipcodetpl = '';
        $popup_html = '';
        if ($values['enable'] == 1
            && ($this->context->controller->php_self == 'product'
            || $this->context->controller->php_self == 'order'
            )) {
            // code added by rishabh on 14 july to hide the message if product is in disabled product list
            $disable = $values['disable_add_to_cart'];
            $product_id = Tools::getValue('id_product');
            if (isset($values['product_list'])) {
                if (in_array($product_id, $values['product_list'])) {
                    $disable = 0;
                }
            }
            $this->context->smarty->assign('disable_addtocart', $disable);
            $this->context->controller->addJS(($this->_path) . 'views/js/front/velovalidation.js');
            $this->context->controller->addCSS($this->_path . 'views/css/front/location_product.css');
            $this->context->controller->addJS($this->_path . 'views/js/front/location_product.js');
            $zipcodetpl = $this->display(__FILE__, 'views/templates/hook/zipcode.tpl');
        }
        if ($values['enable'] == 1 && $values['disable_popup'] == 0) { //Changes start by Shivam Bansal on 09/01/2021 for adding the functionality of popup on/off
            /* Start Code added by Tarun on 25-August-2020 for popup  */
            $popup_html = $this->getZipcodePopupHtml();
            /* End Code added by Tarun on 25-August-2020 for  popup  */
        }
        return $popup_html.$zipcodetpl;
    }
    
    /**
     * Function responsible to Fetching the "Set Your Zipcode" popup Template HTML content. 
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return template
     */
    public function getZipcodePopupHtml()
    {
        $popup_html = '';
        if ($this->context->cookie->zip_code_entered != false) {
            $postcode = $this->context->cookie->zip_code_entered;
            $this->context->smarty->assign('current_zipcode', $postcode);
        }
        $zipcodepage = $this->context->link->getModuleLink(
            $this->name,
            'availability',
            array(),
            (bool)Configuration::get('PS_SSL_ENABLED')
        );
        if ((bool) Configuration::get('PS_SSL_ENABLED')) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        //changes done by tarun for the custom css and js issue for popup
        $values = json_decode(Configuration::get('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE'), true);
        $this->context->smarty->assign(
            array(
                'custom_js' => $values['custom_js'],
                'custom_css' => $values['custom_css'],
            )
        );
        //changes over
        $time = time();
        $this->context->smarty->assign('cross_img_path', $module_dir . 'productavailabilitycheckbyzipcode/views/img/front/close.png?time='.$time);
        $this->context->smarty->assign("kbsrc", $module_dir . $this->name);
        $this->context->smarty->assign('kb_color_data', 'black');
        $this->context->controller->addCSS($this->_path . 'views/css/front/zipcodepopup.css', 'all');
        $this->context->controller->addCSS($this->_path . 'views/css/front/kbzipcodelayout.css', 'all');
        $this->context->smarty->assign('zipcodepage', $zipcodepage);
        $popup_html .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'productavailabilitycheckbyzipcode/views/templates/hook/zipcode_popup.tpl');
        return $popup_html;
    }
    
    /**
     * Hook responsible to display the Link for "Set Your Zipcode" on the header
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return template
     */
    public function hookDisplayNav1()
    {
        $values = json_decode(Configuration::get('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE'), true);
        if ($values['disable_popup'] == 0) {
            $popup_link_html = $this->getZipcodePopupLinkHtml();
            return $popup_link_html;
        }
    }

    /**
     * Function responsible to Fetching the "Set Your Zipcode" link Template HTML content. 
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return template
     */
    public function getZipcodePopupLinkHtml()
    {
        $link_html = '';
        $values = json_decode(Configuration::get('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE'), true);
        if ($values['enable'] == 1) {
            if ($this->context->cookie->zip_code_entered != false) {
                $postcode = $this->context->cookie->zip_code_entered;
                $this->context->smarty->assign('current_zipcode', $postcode);
            }
            $link_html .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'productavailabilitycheckbyzipcode/views/templates/hook/zipcode_popup_link.tpl');
        }
        return $link_html;
    }
    
    /**
     * Hook responsible to check whether all the products available on cart can be shipped to the customer zipcode or not on the basis of the zone product-mapping, and if somehow some products are not available then disable the continue button and show the error message for the unavailable products
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return template
     */
    public function hookDisplayBeforeCarrier($params = array())
    {
        $values = json_decode(Configuration::get('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE'), true);
        /**
         * check if module and disable_add_to_cart condition is enabled or not, if not then return 
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        if (($values['enable'] == 1) && ($values['disable_add_to_cart'] == 1)) {
            $unavailableProductList = array();
            $validateOrder = true;
            /**
             * This is the default prestashop function to fetch the listing of all the products , added in cart
             * @date 14-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
            $products = Context::getContext()->cart->getProducts();
            $product_ids = array();
            foreach ($products as $product) {
                $product_ids[(int)$product['id_product']] = $product;
            }
            /**
             * Fetch the customer id_address_delivery, as we will fetch the customer zipcode from the address object
             * @date 14-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
            $currentDeliveryAddress = Context::getContext()->cart->id_address_delivery;
            $address = new Address((int)$currentDeliveryAddress);
            /**
             * comment the below line and use "preg_replace('/\s+/', '',$address->postcode)" as if the zipcode format is NNN NNN, in that case our module doesnt work, so added fixes to remove the white spaces from the zipcode
             * VG feb2023 format-issue
             * @date 14-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
//            $currentPostCode = $address->postcode;
            $currentPostCode = preg_replace('/\s+/', '',$address->postcode);
            
            /**
             * check whether the product in cart can be shipped to the customer address or not using the product-zone mapping rules, as we use the foreach loop to find the mapped zone with each product and on the basis of zones we check the whether the customer zipcode is inside the zone or not
             * @date 14-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
            foreach ($product_ids as $key => $product_details) {
                // code added by rishabh to enable checkout for those product which are in disabled product list
                $disable = 1;
                /**
                 * use $disable variable, to mark whether the product is need to be checked or not, to implement the "Disabled Products" module functionality
                 * @date 14-02-2023
                 * @author 
                 * @commenter Vishal Goyal
                 */
                if (isset($values['product_list'])) {
                    $product_id = $key;
                    if (in_array($product_id, $values['product_list'])) {
                        $disable = 0;
                    }
                }
                if ($disable == 1) {
                    // changes by rishabh jain
                    $filter_cond = '';
                    /**
                     * below code is use to make the module compatible with our marketplace module , as in marketplace we have the functionality so seller can also set the zipcode for their products
                     * @date 14-02-2023
                     * @author 
                     * @commenter Vishal Goyal
                     */
                    if (Module::isInstalled('kbmarketplace') && Module::isEnabled('kbmarketplace')) {
                        if (Configuration::get('KB_MARKETPLACE') !== false
                            && Configuration::get('KB_MARKETPLACE') == 1) {
                            $mp_config = json_decode(Configuration::get('KB_MARKETPLACE_CONFIG'), true);
                            if (isset($mp_config['enable_product_avaialability_compatibility']) && $mp_config['enable_product_avaialability_compatibility'] == 1) {
                                $seller = KbSellerProduct::getSellerByProductId($key);
                                if (is_array($seller) && count($seller) > 0) {
                                    $filter_cond = ' and is_seller_zone = '.(int) $seller['id_seller'];
                                }
                            }
                        }
                    }
                    // changes over
                    /**
                     * This the main sql query which checked whether the product is available on the customer zipcode or not using data saved in DB.
                     * @date 14-02-2023
                     * @author 
                     * @commenter Vishal Goyal
                     */
                    $checkProductZipcodeMappingExist = 'SELECT * FROM `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` zzm INNER JOIN `'._DB_PREFIX_.'kb_pacbz_product_zone_mapping` pzm on (zzm.zone_id = pzm.zone_id) INNER JOIN `'._DB_PREFIX_.'kb_pacbz_zones` pz on (zzm.zone_id = pz.id_kb_pacbz_zones)'.
                     'where zzm.zipcode ="' .pSQL($currentPostCode). '" and zzm.availability = 1 and pzm.product_id ='.(int)$key . $filter_cond;
                    $result = Db::getInstance()->ExecuteS($checkProductZipcodeMappingExist);
                    if (!count($result)) {
                        $validateOrder = false;
                        $unavailableProductList[] = $product_details['name'];
                    }
                }
            }
            /**
             * Return the template data with the unavailable product listing so that the same error message is displayed on the carrier page.
             * @date 14-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
            if (!$validateOrder) {
                $this->context->smarty->assign(
                    array(
                        'unavailableProductList' => $unavailableProductList,
                        'disable_order_placement' => true
                    )
                );
                return $this->display(__FILE__, 'views/templates/hook/product_not_available.tpl');
            }
        }
    }

    /**
     * Hook responsible to display the "Check Availability" template on the product page, so that customer can check the product availability on the basis of their zipcodes
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return template
     */
    public function hookdisplayFooterProduct()
    {
        $postcode = '';
        $context = Context::getContext();
        $id_customer = $this->context->cookie->id_customer;
        /**
         * check if customer is logged in or not, so if customer is logged In then we dont ask the zipcode, insted just show the availbility message
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        if ($id_customer != false) {
            $customer= new Customer((int)$id_customer);
            $address_id = Address::getFirstCustomerAddressId((int)$id_customer);
            $customer = Address::initialize((int)$address_id);
            $postcode = $customer->postcode;
        }
        
        /**
         * check if zipcode cookie is set or not, if set then use the same zipcode which customer has used last time.
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        if ($this->context->cookie->zip_code_entered != false) {
            $postcode = $this->context->cookie->zip_code_entered;
        } else if ($id_customer != false) {
            $customer= new Customer((int)$id_customer);
            $address_id = Address::getFirstCustomerAddressId((int)$id_customer);
            $customer = Address::initialize((int)$address_id);
            $postcode = $customer->postcode;
        }
        /* Code added by rishabh on 14th July so as to disable the center.tpl file load when the product is in disabled product list */
        $product_id = Tools::getValue('id_product');
        $disable = 0;
        // changes by rishabh jain to fix the out of stock issue
        $prod_obj = new Product($product_id);
        $product_available_stock = StockAvailable::getQuantityAvailableByProduct($prod_obj->id);
//        $attributes = $prod_obj->getAttributesResume($this->context->language->id);
//        if ($attributes && count($attributes) > 0 && $prod_obj->id != '' && $prod_obj->id > 0) {
//            foreach ($attributes as &$attribute) {
//                // changes by rishabh jain
//                $attribute['stock_available'] = StockAvailable::getQuantityAvailableByProduct(
//                    (int)$prod_obj->id,
//                    $attribute['id_product_attribute']
//                );
//            }
//        } else {
//            $attributes = array();
//        }
//        $this->context->smarty->assign('product_attributes', Tools::jsonEncode($attributes));
//        $this->context->smarty->assign('product_available_stock', $product_available_stock);
        // changes over
        //Hook to check availability of the product at front
        $values = json_decode(Configuration::get('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE'), true);
        /**
         * use $disable variable, to mark whether the product is need to be checked or not, to implement the "Disabled Products" module functionality
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        if (isset($values['product_list'])) {
            if (in_array($product_id, $values['product_list'])) {
                $disable = 1;
            }
        }
        // changes by rishabh jain to hide the zipcode block for out of stock product
        /**
         * below code is resposible to check whether the product si abilable for order or not, if not then dont show the "Check Availability" template on the product page
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        if (!$product_available_stock) {
            $query = 'Select out_of_stock from ' . _DB_PREFIX_ . 'stock_available where id_product = '.(int)$product_id;
            $out_of_stock = Db::getInstance()->getValue($query);
            if ((int)$out_of_stock == 0) {
                $disable = 1;
                if (Configuration::get('PS_ORDER_OUT_OF_STOCK')) {
                    $disable = 0;
                }
            } else if ((int)$out_of_stock == 2) {
                $is_out_of_stock_orders_allowed = Configuration::get('PS_ORDER_OUT_OF_STOCK');
                if ($is_out_of_stock_orders_allowed == 0) {
                    $disable = 1;
                }
            }
        }
        // changes over
        if ($values['enable'] == 1 && $disable == 0) {
            if (Tools::getShopProtocol() == 'https://') {
                $av_protocol_name = _PS_BASE_URL_SSL_ . _MODULE_DIR_;
            } else {
                $av_protocol_name = _PS_BASE_URL_ . _MODULE_DIR_;
            }
            $controller_path = $this->context->link->getModuleLink(
                $this->name,
                'availability'
            );
            /* Line added by rishabh to autofill zipcode */
            $this->context->smarty->assign('postcode', $postcode);
         
            $this->context->smarty->assign(
                array(
                    'display_enable' => $values['enable'],
                    'custom_js' => $values['custom_js'],
                    'custom_css' => $values['custom_css'],
                    'av_image_path' => $av_protocol_name,
                    'check_ajax_path' => $controller_path,
                    'kb_id_product' => Tools::getValue('id_product'),
                    'disable_addtocart' => $values['disable_add_to_cart']
                )
            );
            return $this->display(__FILE__, 'views/templates/hook/center.tpl');
        }
    }

    /**
     * Function responsible to get the default settings, save the default setting & General setting page of the module
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return template
     */
    public function getContent()
    {
        /**
         * Ajax triggered whenever admin search any product in "Disabled product" search field, and return the products list containing the searched keywords
         * @date 03-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        if (Tools::getvalue('ajaxproductaction')) {
            echo $this->ajaxproductlist();
            die;
        }
        
        $formvalue = array();
        $languages = Language::getLanguages(true);
        $config = Configuration::get('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE');
        $formvalue = json_decode($config, true);
        $output = null;

        //If Save Button is clicked
        /**
         * below code is used to save the configuration form data in the DB and is triggered on clicking the save button.
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        if (Tools::isSubmit('product_availability_check_by_zipcode')) {
            $formvalue = Tools::getValue('product_availability_check_by_zipcode');
            /**
             * Save the disabled product IDs in configuration table
             * @date 03-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
            if(!empty(Tools::getValue('kb_home_specific_product_items'))){
                $formvalue['product_list'] = explode(',', Tools::getValue('kb_home_specific_product_items'));
            }
            Configuration::updateValue('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE_DISABLED', json_encode(Tools::getValue('kb_home_specific_product_items')));
            //Storing value in database
            Configuration::updateValue('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE', json_encode($formvalue));
            $output .= $this->displayConfirmation(
                $this->l('Configuration has been saved successfully.')
            );
        }

        $action = AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules');
        $oss_admin_css =  'productavailabilitycheckbyzipcode/views/css/admin/product_availability_check_by_zipcode.css';
        $oss_admin_js = 'productavailabilitycheckbyzipcode/views/';
        $oss_admin_js .= 'js/admin/product_availability_check_by_zipcode_admin.js';

        //Checking version
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $ps_version = 15;
        } else {
            $ps_version = 16;
            if (Tools::getShopProtocol() == 'https://') {
                $this->context->controller->addJS(_PS_BASE_URL_SSL_._MODULE_DIR_.$oss_admin_js);
                $this->context->controller->addCSS(_PS_BASE_URL_SSL_._MODULE_DIR_.$oss_admin_css);
            } else {
                $this->context->controller->addJS(_PS_BASE_URL_._MODULE_DIR_.$oss_admin_js);
                $this->context->controller->addCSS(_PS_BASE_URL_._MODULE_DIR_.$oss_admin_css);
            }
        }
        $this->context->controller->addJS(($this->_path) . 'views/js/admin/velovalidation.js');
        $this->context->controller->addJS(_PS_MODULE_DIR_ . 'productavailabilitycheckbyzipcode/views/js/admin/zonevalidation.js');

        $this->context->smarty->assign('show_toolbar', false);
        /**
         * below code is responsible for Creating configuration setting form
         * @date 14-02-2023
         * @commenter Vishal Goyal
         */
        $enable_plugin = array(
            'label' => $this->l('Enable Plugin'),
            'type' => 'switch',
            'hint' => $this->l('Enable product availability check by Zip-code.'),
            'name' => 'product_availability_check_by_zipcode[enable]',
            //'required' => true,
            'values' => array(
                array(
                    'value' => 1,
                    'id' => 'product_availability_check_by_zipcode[enable]_on',
                ),
                array(
                    'value' => 0,
                    'id' => 'product_availability_check_by_zipcode[enable]_off',
                ),
            ),
        );
        /**
         * Commented below code, as earlier we use the select functioanlity for the "Disable product", but now we are using autocomplete functioanlity.
         * @date 14-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        // Code added by rishabh on 14th july to display the product list so as admin can select products for which admin want to disable the plugin */
        //$sql = 'SELECT a.id_product as id_option, b.name as name FROM ' . _DB_PREFIX_ . 'product as a INNER JOIN ' . _DB_PREFIX_ . 'product_lang as b on (b.id_product = a.id_product) where b.id_lang = ' . (int) $this->context->language->id;
        //$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        /* for reference name as well */
//        $sql8 = 'SELECT l.name,l.id_product,p.reference FROM `'
//            ._DB_PREFIX_.'product_lang` as l inner join `'._DB_PREFIX_.'product` as p' .
//            ' on l.id_product = p.id_product group by l.id_product';
        
        //changes by tarun for the issue reported by gopi
//        $sql8 = 'SELECT l.name,l.id_product,p.reference FROM `'
//            ._DB_PREFIX_.'product_lang` as l inner join `'._DB_PREFIX_.'product` as p' .
//            ' on l.id_product = p.id_product WHERE l.id_lang ='.(int) $this->context->language->id.' group by l.id_product';
//        //changes over
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
//        $product_list = array(
//            'type' => 'select',
//            'label' => $this->l('Disabled Products'), 
//            'multiple' => true,
//            'class' => 'chosen',
//            'hint' => $this->l('Select product from the list for which you want to disable this module.You can leave it to empty if you want to enable this module for all product'),
//            'name' => 'product_availability_check_by_zipcode[product_list][]', 
//            'options' => array(
//                'query'=> $option1,
//                'id' =>  'id_module',
//                'name'=>  'name'
//            )
//        );
        
        /**
         * Below code is responsible to fetch the disabled products saved in DB, and then assign the same to TPL
         * @date 03-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        
        $kb_home_page_specific_product_data = json_decode(Configuration::get('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE_DISABLED'), true);

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
        
        $product_list_new = array(
            'type' => 'text',
            'label' => $this->l('Disabled Products'), 
            'hint' => $this->l('Select product from the list for which you want to disable this module.You can leave it to empty if you want to enable this module for all product'),
            'name' => 'product_availability_check_by_zipcode_disabled', 
        );
        
        $display_delivery = array(
            'label' => $this->l('Display Delivery Date'),
            'type' => 'switch',
            'hint' => $this->l('Display number of days of delivery and date of delivery of the product.'),
            'name' => 'product_availability_check_by_zipcode[delivery]',
            //'required' => true,
            'values' => array(
                array(
                    'value' => 1,
                    'id' => 'product_availability_check_by_zipcode[delivery]_on',
                ),
                array(
                    'value' => 0,
                    'id' => 'product_availability_check_by_zipcode[delivery]_off',
                ),
            ),
        );
        //Changes start by Shivam Bansal on 09/01/2021 for adding the functionality of popup on/off
        $popup_disable = array(
            'label' => $this->l('Disable Zipcode Popup'),
            'type' => 'switch',
            'hint' => $this->l('If enabled then zipcode popup will not opens on the Store.'),
            'desc' => $this->l('If enabled then zipcode popup will not opens on the Store.'),
            'name' => 'product_availability_check_by_zipcode[disable_popup]',
            'values' => array(
                array(
                    'value' => 1,
                    'id' => 'product_availability_check_by_zipcode[disable_popup]_on',
                ),
                array(
                    'value' => 0,
                    'id' => 'product_availability_check_by_zipcode[disable_popup]_off',
                ),
            ),
        );
        //Changes end by Shivam Bansal on 09/01/2021 for adding the functionality of popup on/off
        $disable_addcart = array(
            'label' => $this->l('Check Cart'),
            'type' => 'switch',
            'hint' => $this->l('If enabled then add to cart button will be enable only if product is available for the Postal code.'),
            'desc' => $this->l('If enabled then add to cart button will be enable only if product is available for the Postal code.'),
            'name' => 'product_availability_check_by_zipcode[disable_add_to_cart]',
            'values' => array(
                array(
                    'value' => 1,
                    'id' => 'product_availability_check_by_zipcode[disable_add_to_cart]_on',
                ),
                array(
                    'value' => 0,
                    'id' => 'product_availability_check_by_zipcode[disable_add_to_cart]_off',
                ),
            ),
        );
        // Creating form
        $this->fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Product Availability Check by Zipcode'),
                ),
                'input' => array(
                    $enable_plugin,
                    $display_delivery,
                    $popup_disable, //Changes by Shivam Bansal on 09/01/2021 for adding the functionality of popup on/off
                    $disable_addcart,
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Custom CSS'),
                        'name' => 'product_availability_check_by_zipcode[custom_css]',
                        'hint' => $this->l('Enter the css to customize your module excluding tags.Ex-" margin:10px; color:red; ",etc'),
                        'class' => 'optn_lookfeel vss-textarea custom_css',
                        'cols' => 100,
                        'rows' => 5
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Custom JS'),
                        'name' => 'product_availability_check_by_zipcode[custom_js]',
                        'hint' => $this->l('Enter the js to customize your module excluding tags.Ex-" alert(1234); "'),
                        'class' => 'optn_lookfeel vss-textarea custom_js',
                        'cols' => 100,
                        'rows' => 5
                    ),
//                    $product_list,
                    $product_list_new,
                    array(
                        'type' => 'html',
                        'name' => '',
                        'html_content' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'productavailabilitycheckbyzipcode/views/templates/admin/showSelectedProducts_home_page.tpl'),
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'kb_home_specific_product_items',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right configure_zip'
                ),
            ),
        );
        
        /**
         * Code resposible for Assigning field value to Helper Form object
         * @date 14-02-2023
         * @commenter Vishal Goyal
         */
        if (isset($formvalue['product_availability_check_by_zipcode']['enable'])) {
            $de_enable = $formvalue['product_availability_check_by_zipcode']['enable'];
            $delivery = $formvalue['product_availability_check_by_zipcode']['delivery'];
            $popup = $formvalue['product_availability_check_by_zipcode']['disable_popup']; //Changes by Shivam Bansal on 09/01/2021 for adding the functionality of popup on/off
            $disable_addtocart = $formvalue['product_availability_check_by_zipcode']['disable_add_to_cart'];
            $custom_css = $formvalue['product_availability_check_by_zipcode']['custom_css'];
            $custom_js = $formvalue['product_availability_check_by_zipcode']['custom_js'];
            $field_value = array(
                'product_availability_check_by_zipcode[enable]' => $de_enable,
                'product_availability_check_by_zipcode[delivery]' => $delivery,
                'product_availability_check_by_zipcode[disable_popup]' => $popup, //Changes by Shivam Bansal on 09/01/2021 for adding the functionality of popup on/off
                'product_availability_check_by_zipcode[disable_add_to_cart]' => $disable_addtocart,
                'product_availability_check_by_zipcode[custom_css]' => $custom_css,
                'product_availability_check_by_zipcode[custom_js]' => $custom_js,
            );
            // changes by rishabh on 14 th july
            if (isset($formvalue['product_list'])) {
                $field_value['product_availability_check_by_zipcode[product_list][]'] = $formvalue['product_list'];
            } else {
                $field_value['product_availability_check_by_zipcode[product_list][]'] = array();
            }
        } else {
            $field_value = array(
                'product_availability_check_by_zipcode[enable]' => $formvalue['enable'],
                'product_availability_check_by_zipcode[delivery]' => $formvalue['delivery'],
                'product_availability_check_by_zipcode[disable_popup]' => $formvalue['disable_popup'], //Changes by Shivam Bansal on 09/01/2021 for adding the functionality of popup on/off
                'product_availability_check_by_zipcode[disable_add_to_cart]' => isset($formvalue['disable_add_to_cart']) ? $formvalue['disable_add_to_cart']:0,
                'product_availability_check_by_zipcode[custom_css]' => $formvalue['custom_css'],
                'product_availability_check_by_zipcode[custom_js]' => $formvalue['custom_js'],
            );
            if (isset($formvalue['product_list'])) {
                $field_value['product_availability_check_by_zipcode[product_list][]'] = $formvalue['product_list'];
            } else {
                $field_value['product_availability_check_by_zipcode[product_list][]'] = array();
            }
        }
        
        /**
         * Added the autocomplete JQuery file for autocomplete functioanlity
         * @date 03-02-2023
         * @author 
         * @commenter Vishal Goyal
         */
        
        $field_value['kb_home_specific_product_items'] = json_decode(Configuration::get('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE_DISABLED'), true);
        $field_value['product_availability_check_by_zipcode_disabled'] = "";
        $this->context->smarty->assign('kb_admin_link', $this->context->link->getAdminLink('AdminModules', true).'&configure='. $this->name.'&ajaxproductaction=true');
        $this->context->controller->addJs(($this->_path) . '/views/js/admin/jquery.autocomplete.js');

        //Creating Helper Form Oject and assigning object values
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->fields_value = $field_value;

        $languages = Language::getlanguages(true);
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
        }

        $helper->name_controller = $this->name;
        $helper->languages = $languages;
        $helper->default_form_language = $this->context->language->id;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->show_cancel_button = false;
        $helper->submit_action = $action;
        $helper->no_link = true;
        $form = $helper->generateForm(array($this->fields_form));

        $this->context->smarty->assign('default_lang', $this->context->language->id);
        $this->context->smarty->assign('form', $form);
        $this->context->smarty->assign('path', $this->getPath());
        $this->context->smarty->assign('firstCall', false);
        $this->context->smarty->assign('version', $ps_version);

        $tpl = 'Form_custom.tpl';
        $helper = new Helper();
        $helper->module = $this;
        $helper->override_folder = 'helpers/';
        $helper->base_folder = 'form/';
        $helper->base_tpl = 'view_custom.tpl';
        $helper->setTpl($tpl);
        $tpl = $helper->generate();

        $output = $output . $tpl;
        
        return $output;
    }

    /**
     * Function responsible to Fetch and return the URL upto the module directort
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return String
     */
    public function getPath()
    {
        //Function to get Base Directory
        if (Tools::getShopProtocol() == 'https://') {
            $custom_ssl_var = 1;
        } else {
            $custom_ssl_var = 0;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__;
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__;
        }
        return $module_dir;
    }

    /**
     * Function to get default settings of the module.
     * @date 19-01-2023
     * @author 
     * @commenter Vishal Goyal
     * @return array
     */
    private function getDefaultSettings()
    {
        //Function to set default value
        $settings = array(
            'enable' => 0,
            'delivery' => 0,
            'disable_popup' => 0,//Changes start by Shivam Bansal on 09/01/2021 for adding the functionality of popup on/off
            'disable_add_to_cart' => 0,
            'custom_js' => '',
            'custom_css' => ''
        );

        return $settings;
    }

    /**
     * Function to get default settings for the Add new Zone form.
     * @date 19-01-2023
     * @author 
     * @commenter Vishal Goyal
     * @return array
     */
    public function setDefaultAddZone()
    {
        //Function to set default value in add zone page
        $settings = array(
            'availability' => 1,
            'upload_csv' => 0,
            'zone_name' => '',
            'zip-codes' => '',
            'deliver_by' => '',
            'selected_file' => '',
        );
        Configuration::updateValue('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE_ADD_ZONE', json_encode($settings));
    }

    public function setDefaultAddProduct()
    {
        //Function to set default values in Add new product page
        $settings = array(
            'upload_csv' => 0,
            'search_product' => '',
            'selected_file' => '',
            'select_zone' => '0',
        );
        Configuration::updateValue('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE_ADD_PRODUCT', json_encode($settings));
    }
}
