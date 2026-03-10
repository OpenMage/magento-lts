<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_SalesRule
 */

/**
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Rule_Product extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/rule_product');
    }
}
