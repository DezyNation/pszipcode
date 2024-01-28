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

include_once(_PS_MODULE_DIR_ . 'productavailabilitycheckbyzipcode/classes/KbZipcodeRequest.php');
class ProductavailabilitycheckbyzipcodeAvailabilityModuleFrontController extends ModuleFrontController
{
    //Conroller for validation of zipcode in Front-End
    public function init()
    {
        parent::init();
    }
    
    /**
     * Function responsible to process Ajax requests, save the zipcode in cookie, Create the zipcode request object if the seacrhed zipcode is not available in any zone and save that,Return message according to the specific scenerio
     * @date 23-01-2023
     * @author 
     * @commenter Vishal Goyal
     */
    public function postProcess()
    {
        parent::postProcess();
        //Handle Ajax request
        $msg = 0;
        $result = array();
        if (Tools::isSubmit('ajax')) {
            //changes by tarun to set the popup zipcode
            if (Tools::isSubmit('method')) {
                $renderhtml = false;
                switch (Tools::getValue('method')) {
                    case 'setpopupzipcode':
                        /**
                         * Below function is responsible for setting the zipcode in cookie and add/update the zipcode request count if the zipcode is not mapped with any zone, trigerred when we update the zipcode through popup
                         * @date 14-02-2023
                         * @author 
                         * @commenter Vishal Goyal
                         */
                        $this->json = $this->setpopupzipcode();
                        break;
                }
                if (!$renderhtml) {
                    /*
                     * Changes done by Vishal to replace Tools::jsonDecode by json_decode or Tools::jsonEncode by json_encode for making the module compatible with PS 8.0
                     * VGmar2023 Setting-Json_Decode
                     * @date 16-02-2023
                     * author Vishal Goyal
                     */
                    echo json_encode($this->json);
                }
                die;
            }
            //changes over
            /**
             * Below function is responsible for checking whether the products is available for the request zipcode or not, set the zipcode in cookie if cookie is not set, and  add/update the zipcode request count if the zipcode is not mapped with any zone,
             * @date 14-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
            $values = json_decode(Configuration::get('PRODUCT_AVAILABILITY_CHECK_BY_ZIPCODE'), true);
            //changes by tarun to resolve the issue to accept other format zipcodes
            $data = Tools::getValue('zip_code_value');
            //changes over
            /**
             * We first checked whether the cookie is set or not for zipcode, if not then set the zipcode to cookie, and add/update the zipcode request count if the zipcode is not mapped with any zone,
             * @date 14-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
            if (!($this->context->cookie->__isset('zip_code_entered') && (strcmp($this->context->cookie->zip_code_entered, $data) == 0))) {
                /* code added by rishabh on 16th july to store entered pin in cookie */
                $this->context->cookie->__set('zip_code_entered', $data);
                //changes over
                $sql = 'Select id_request, controller_name from ' . _DB_PREFIX_ . 'kb_zipcodes_requests where zipcode = "' . pSQL($data) . '"';
                $results = Db::getInstance()->executeS($sql);
                $controller_found = 0;
                $controller_name = $this->context->controller->php_self;
                if ($controller_name == '') {
                    $controller_name = Tools::getValue('controller');
                }
                if (isset($results) && !empty($results)) {
                    foreach ($results as $key) {
                        if ($key['controller_name'] == $controller_name) {
                            $controller_found = 1;
                            $ip_request_id = $key['id_request'];
                            break;
                        }
                    }
                    if ($controller_found == 1) {
                        $zipcoderequest_object = new KbZipcodeRequest($ip_request_id);
                        $zipcoderequest_object->count = $zipcoderequest_object->count + 1;
                        $zipcoderequest_object->update();
                    } else {
                        $zipcoderequest_object = new KbZipcodeRequest();
                        $zipcoderequest_object->zipcode = $data;
                        $zipcoderequest_object->controller_name = $controller_name;
                        $zipcoderequest_object->id_shop = $this->context->shop->id;
                        $zipcoderequest_object->count = 1;
                        $zipcoderequest_object->add();
                    }
                } else {
                    $zipcoderequest_object = new KbZipcodeRequest();
                    $zipcoderequest_object->zipcode = $data;
                    $zipcoderequest_object->controller_name = $controller_name;
                    $zipcoderequest_object->id_shop = $this->context->shop->id;
                    $zipcoderequest_object->count = 1;
                    $zipcoderequest_object->add();
                }
                /* changes over */
            }
            
            /**
             * Below function is responsible for setting different error message on the basis of differnt conditions like 
             * @date 14-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
            /**
             * Set error message 1 i.e Please enter the zip-code. If zipcode is blank
             * @date 14-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
            if ($data == null) {
                $this->context->smarty->assign('message_value', 1);
                $msg = 1;
            } elseif (Tools::strlen((string)$data) >= 10) {
                /**
                 * Set error message 4 i.e Please enter a valid zip-code. If zipcode lenth is greater than 10
                 * @date 14-02-2023
                 * @author 
                 * @commenter Vishal Goyal
                 */
                $this->context->smarty->assign('message_value', 4);
                $msg = 4;
            } elseif (preg_match('/^[a-zA-Z 0-9-]+$/', $data)) {
                $data = trim($data);
                $data = Tools::stripslashes($data);
                $data = addslashes($data);
                $id_product = Tools::getValue('id_product');
                $filter_cond = '';
                if (Module::isInstalled('kbmarketplace') && Module::isEnabled('kbmarketplace')) {
                    if (Configuration::get('KB_MARKETPLACE') !== false
                        && Configuration::get('KB_MARKETPLACE') == 1) {
                        $mp_config = json_decode(Configuration::get('KB_MARKETPLACE_CONFIG'), true);
                        if (isset($mp_config['enable_product_avaialability_compatibility']) && $mp_config['enable_product_avaialability_compatibility'] == 1) {
                            $seller = KbSellerProduct::getSellerByProductId($id_product);
                            /*
                            * We have added the compatibility with our deal manager plugin and we are using the function of that module class.
                            */
                            if (is_array($seller) && count($seller) > 0) {
                                $filter_cond = ' and pz.is_seller_zone = '.(int) $seller['id_seller'];
                            } else {
                                $filter_cond = ' and pz.is_seller_zone = "0"';
                            }
                        } else {
                            $filter_cond = ' and pz.is_seller_zone = "0"';
                        }
                    } else {
                        $filter_cond = ' and pz.is_seller_zone = "0"';
                    }
                }
                /**
                 * below code is use to fetch the zones mapped with the requested zipcode
                 * @date 14-02-2023
                 * @author 
                 * @commenter Vishal Goyal
                 */
                $sql = 'SELECT * FROM `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` as zzm INNER JOIN `'._DB_PREFIX_.'kb_pacbz_zones` pz on (zzm.zone_id = pz.id_kb_pacbz_zones)'.
                        ' where zipcode ="' .pSQL($data). '" and availability = 1 and pz.id_shop= ' . $this->context->shop->id . ' ' . $filter_cond;
                $count = Db::getInstance()->ExecuteS($sql);
                if (isset($count[0]['zone_id']) && $count[0]['zone_id'] != null) {
                    // changes by rishabh jain
                    // changes over
                    /**
                     * below code is checking whether the zone fetched from the above query is mapped with the product or not, otherwise show the unavailability message
                     * @date 14-02-2023
                     * @author 
                     * @commenter Vishal Goyal
                     */
                    $sql0 = 'SELECT * FROM `'._DB_PREFIX_.'kb_pacbz_product_zone_mapping` as pzm INNER JOIN `'._DB_PREFIX_.'kb_pacbz_zones` pz on (pzm.zone_id = pz.id_kb_pacbz_zones)'.
                        ' where product_id ="' .pSQL($id_product).'" and zone_id = "'.pSQL($count[0]['zone_id']).'" and pz.id_shop= ' . $this->context->shop->id . ' '. $filter_cond;
                    $results0 = Db::getInstance()->ExecuteS($sql0);
                    // changes by rishabh jain for marketplace compatibility
                    if (isset($results0) && $results0 != null) {
                        /**
                         * below code is responsible for fetching the delivery day Gap according to the zone.
                         * @date 14-02-2023
                         * @author 
                         * @commenter Vishal Goyal
                         */
                        $sql = 'SELECT deliver_by FROM `'._DB_PREFIX_.'kb_pacbz_zone_zipcode_mapping` as zzm INNER JOIN `'._DB_PREFIX_.'kb_pacbz_zones` pz on (zzm.zone_id = pz.id_kb_pacbz_zones)'.
                        ' where zipcode ="' .pSQL($data). '" and availability = 1';
                        $results = Db::getInstance()->ExecuteS($sql);
                        foreach ($results as $row) {
                            $delivery_date = date('d-M-Y', strtotime(date('Y-m-d')) + (24*3600*$row['deliver_by']));
                            $this->context->smarty->assign('delivery_date', $delivery_date);
                            $this->context->smarty->assign('number_of_days', $row['deliver_by']);
                        }
                        // changes  by rishabh jain to fix the attribute issue
                        $this->context->smarty->assign('message_value', 2);
                        $msg = 2;
                        $groups = array();
                        /**
                         * below code is responsible for checking whether the product with attribues are in stock or not, otherwise show the out of stock error message
                         * @date 14-02-2023
                         * @author 
                         * @commenter Vishal Goyal
                         */
                        if (Tools::getIsset('group')) {
                            foreach (Tools::getValue('group') as $gp) {
                                $groups[] = $gp;
                            }
                            $id_product_attribute = $this->getIdProductAttributesByIdAttributes(Tools::getValue('id_product'), $groups);
                            $product_attribute_available_stock = StockAvailable::getQuantityAvailableByProduct(
                                (int)Tools::getValue('id_product'),
                                $id_product_attribute
                            );
                            if (!$product_attribute_available_stock) {
                                $out_of_stock = Configuration::get('PS_ORDER_OUT_OF_STOCK');
                                if ($out_of_stock == 0) {
                                    $this->context->smarty->assign('message_value', 5);
                                    $msg = 5;
                                }
                            }
                        }
                    } else {
                        $this->context->smarty->assign('message_value', 3);
                        $msg = 3;
                    }
                } else {
                    $this->context->smarty->assign('message_value', 3);
                    $msg = 3;
                }
            } else {
                $this->context->smarty->assign('message_value', 4);
                $msg = 4;
            }
            $this->context->smarty->assign('display_delivery', $values['delivery']);
            $views_path = 'productavailabilitycheckbyzipcode/views/';
            $result['msg'] = $msg;
            $result['html'] = $this->context->smarty->fetch(_PS_MODULE_DIR_. $views_path. 'templates/front/message.tpl');
            header('Content-Type: application/json', true);
            /*
             * Changes done by Vishal to replace Tools::jsonDecode by json_decode or Tools::jsonEncode by json_encode for making the module compatible with PS 8.0
             * VGmar2023 Setting-Json_Decode
             * @date 16-02-2023
             * author Vishal Goyal
             */
            echo json_encode($result);
            die;
        }
    }

    /**
     * Below function is responsible for setting the zipcode in cookie and add/update the zipcode request count if the zipcode is not mapped with any zone, trigerred when we update the zipcode through popup
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     */
    protected function setpopupzipcode()
    {
        $json = array();
        $zipcode = Tools::getValue('zipcode');
        $this->context->cookie->__set('zip_code_entered', $zipcode);
        if (isset($this->context->cookie->zip_code_entered)) {
            /**
             * Check whether the requested zipcode's request is already present in DB or not
             * @date 14-02-2023
             * @author 
             * @commenter Vishal Goyal
             */
            $sql = 'Select id_request, controller_name from ' . _DB_PREFIX_ . 'kb_zipcodes_requests where zipcode = "' . pSQL($zipcode) . '" and id_shop= ' . $this->context->shop->id;
            $results = Db::getInstance()->executeS($sql);
            $controller_found = 0;
            $controller_name = $this->context->controller->php_self;
            if ($controller_name == '') {
                $controller_name = Tools::getValue('controller');
            }
            if (isset($results) && !empty($results)) {
                foreach ($results as $key) {
                    if ($key['controller_name'] == $controller_name) {
                        $controller_found = 1;
                        $ip_request_id = $key['id_request'];
                        break;
                    }
                }
                if ($controller_found == 1) {
                    /**
                     * If the zipcode is not mapped with any zone, then we will create a new object of KbZipcodeRequest and save the same for "Zipcode Requests" functionality
                     * @date 14-02-2023
                     * @author 
                     * @commenter Vishal Goyal
                     */
                    $zipcoderequest_object = new KbZipcodeRequest($ip_request_id);
                    $zipcoderequest_object->count = $zipcoderequest_object->count + 1;
                    $zipcoderequest_object->update();
                } else {
                    $zipcoderequest_object = new KbZipcodeRequest();
                    $zipcoderequest_object->zipcode = $zipcode;
                    $zipcoderequest_object->controller_name = $controller_name;
                    $zipcoderequest_object->id_shop = $this->context->shop->id;
                    $zipcoderequest_object->count = 1;
                    $zipcoderequest_object->add();
                }
            } else {
                $zipcoderequest_object = new KbZipcodeRequest();
                $zipcoderequest_object->zipcode = $zipcode;
                $zipcoderequest_object->controller_name = $controller_name;
                $zipcoderequest_object->id_shop = $this->context->shop->id;
                $zipcoderequest_object->count = 1;
                $zipcoderequest_object->add();
            }
            $json['status'] = true;
        } else {
            $json['status'] = false;
        }
        return $json;
    }
    
    /**
     * Below function is responsible for fetching the product attribute ID, on the basis of selected groups.
     * @date 14-02-2023
     * @author 
     * @commenter Vishal Goyal
     * return Array
     */
    public static function getIdProductAttributesByIdAttributes($id_product, $id_attributes)
    {
//        print_r($id_attributes);
        if (!is_array($id_attributes)) {
            return 0;
        }
//        $sql =  ''
        return DB::getInstance()->getValue(
            'SELECT pac.`id_product_attribute`
            FROM `'._DB_PREFIX_.'product_attribute_combination` pac
            INNER JOIN `'._DB_PREFIX_.'product_attribute` pa 
            ON pa.id_product_attribute = pac.id_product_attribute
            WHERE id_product = '.(int)$id_product.' AND id_attribute IN ('
            .implode(',', array_map('intval', $id_attributes)).')
            GROUP BY id_product_attribute
            HAVING COUNT(id_product) = '. (int) count($id_attributes)
        );
//        die;
    }
}
