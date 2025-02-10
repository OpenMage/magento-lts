<?php
/**
 * SalesRule Model Resource Rule_Product
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Resource_Rule_Product extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('salesrule/rule_product', 'product_rule_id');
    }
}
