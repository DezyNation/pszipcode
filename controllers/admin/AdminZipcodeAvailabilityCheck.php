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

class AdminZipcodeAvailabilityCheckController extends ModuleAdminControllerCore
{
    public function __construct()
    {
        //Redirecting to AdminGlobalZones Controller
        $path = 'index.php?controller=AdminGlobalZones&token=' .Tools::getAdminTokenLite('AdminGlobalZones');
        Tools::redirectAdmin($path);
    }
}
