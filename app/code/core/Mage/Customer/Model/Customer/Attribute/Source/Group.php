<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Customer
 */

/**
 * Customer group attribute source
 *
 * @category   Mage
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Customer_Attribute_Source_Group extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * Retrieve Full Option values array
     *
     * @param bool $withEmpty       Argument has no effect, included for PHP 7.2 method signature compatibility
     * @param bool $defaultValues   Argument has no effect, included for PHP 7.2 method signature compatibility
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('customer/group_collection')
                ->setRealGroupsFilter()
                ->load()
                ->toOptionArray()
            ;
        }
        return $this->_options;
    }
}
