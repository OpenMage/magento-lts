<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_SalesRule
 */

/**
 * SalesRule Model Resource Rule Customer_Collection
 *
 * @category   Mage
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Resource_Rule_Customer_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Collection constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/rule_customer');
    }
}
