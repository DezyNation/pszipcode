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
 * @copyright 2020 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * Class : KbZipcodeRequest
 * Some function related to the zipcode request functionality, generally we use the class concenpt to easily create Admin controllers
 * @date 14-02-2023
 * @author 
 * @commenter Vishal Goyal
 */
class KbZipcodeRequest extends ObjectModel
{
    public $id_request;
    public $zipcode;
    public $controller_name;
    public $count;
    public $date_add;
    public $date_upd;
    public $id_shop;
    
    public static $definition = array(
        'table' => 'kb_zipcodes_requests',
        'primary' => 'id_request',
        'fields' => array(
            'id_request' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'zipcode' => array(
                'type' => self::TYPE_HTML,
                'validate' => 'isCleanHtml'
            ),
            'controller_name' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isName',
            ),
            'count' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'id_shop' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isInt'
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false
            ),
        ),
    );
    
    public function __construct($id_request = null, $id_lang = null)
    {
        parent::__construct($id_request, $id_lang);
    }
}
