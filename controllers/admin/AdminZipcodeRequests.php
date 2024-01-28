<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_.'productavailabilitycheckbyzipcode/classes/KbZipcodeRequest.php');

class AdminZipcodeRequestsController extends ModuleAdminControllerCore
{
    public $all_languages = array();
    protected $kb_module_name = 'productavailabilitycheckbyzipcode';
    
    /**
     * Function responsible to Creating list of all ZIpcode requests by customers
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->allow_export = true;
        $this->context = Context::getContext();
        $this->list_no_link = true;
        $this->table = 'kb_zipcodes_requests';
        $this->className = 'KbZipcodeRequest';
        $this->identifier = 'id_request';
        $this->lang = false;
//        $this->display = 'list';
        parent::__construct();
        $this->toolbar_title = $this->module->l('Knowband Zipcode Requests', 'AdminZipcodeRequestsController');
            
        $this->fields_list = array(
//            'id_request' => array(
//                'title' => $this->module->l('ID', 'AdminKbBlockIPRequestController'),
//                'search' => true,
//                'align' => 'text-center',
//            ),
            'zipcode' => array(
                'title' => $this->module->l('Zipcode', 'AdminZipcodeRequestsController'),
                'search' => true,
                'align' => 'text-center',
            ),
            'total_request_count' => array(
                'title' => $this->module->l('Request Count', 'AdminZipcodeRequestsController'),
                'search' => false,
                'align' => 'text-center',
//                'filter_key' => 'total_request_count'
            ),
            'first_request_date' => array(
                'title' => $this->module->l('First Request Date', 'AdminZipcodeRequestsController'),
                'type' => 'date',
                'filter_key' => 'date_add'
            ),
            'last_request_date' => array(
                'title' => $this->module->l('Last Request Date', 'AdminZipcodeRequestsController'),
                'type' => 'date',
                'filter_key' => 'date_upd',
            )
        );
        
//        print_r(Tools::getValue('kb_block_ip_requestsFilter_total_request_count'));
//        die;
        
        $this->_select = 'SUM(a.count) as total_request_count, MIN(date_add) as first_request_date, MAX(date_upd) as last_request_date';
        $this->_where = 'AND id_shop=' . $this->context->shop->id;
        $this->_group .= 'GROUP by zipcode';
    }
    
    /**
     * Function responsible to Fetch and return the URL upto the module directort
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return String
     */
    protected function getModuleDirUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        return $module_dir;
    }
    
    /**
     * Function responsible get the URL of the store,this function also checks if the store is a secure store or not and returns the URL accordingly
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * @return String
     */
    private function checkSecureUrl()
    {
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $custom_ssl_var = 1;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Function responsible to Assign smarty variables for all default views, list and form, then call parent init functions
     * @date 19-01-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function initContent()
    {
        if (isset($this->context->cookie->kb_redirect_error)) {
            $this->errors[] = $this->context->cookie->kb_redirect_error;
            unset($this->context->cookie->kb_redirect_error);
        }

        if (isset($this->context->cookie->kb_redirect_success)) {
            $this->confirmations[] = $this->context->cookie->kb_redirect_success;
            unset($this->context->cookie->kb_redirect_success);
        }
        
        parent::initContent();
    }
    
    /**
     * Function used to render the form for this controller
     *
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public function renderForm()
    {
        return parent::renderForm();
    }
    
    /** Prestashop Default Function in AdminController
     * @TODO uses redirectAdmin only if !$this->ajax
     * @return bool
     */
    public function postProcess()
    {
        parent::postProcess();
    }
    
    /*
     * Function for returning the absolute path of the module directory
     */
    protected function getKbModuleDir()
    {
        return _PS_MODULE_DIR_.$this->kb_module_name.'/';
    }
    
    /*
     * Default function, used here to include JS/CSS files for the module.
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
    }
    
    /**
    * Function used display toolbar in page header
    */
    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }
}
