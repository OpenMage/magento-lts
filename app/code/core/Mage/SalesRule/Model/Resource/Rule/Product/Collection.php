<?php
/**
 * SalesRule Model Resource Rule Product_Collection
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Resource_Rule_Product_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/rule_product');
    }
}
