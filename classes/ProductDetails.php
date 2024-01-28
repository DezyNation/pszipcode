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
/**
 * Class : ProductDetails
 * Some function related to the creating new zone and displaying them, generally we use the class concenpt to easily create Admin controllers
 * @date 14-02-2023
 * @author 
 * @commenter Vishal Goyal
 */
class ProductDetails extends ObjectModel
{
    public static $definition = array(
        'table' => 'kb_pacbz_zones',
        'primary' => 'id_kb_pacbz_zones',
    );
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
    }
}
