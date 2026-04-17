<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer group attribute source
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Customer_Attribute_Source_Group extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * Retrieve Full Option values array
     *
     * @param  bool  $withEmpty     Argument has no effect, included for PHP 7.2 method signature compatibility
     * @param  bool  $defaultValues Argument has no effect, included for PHP 7.2 method signature compatibility
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
