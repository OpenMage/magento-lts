<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Customer groups api
 *
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Group_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Retrieve groups
     *
     * @return array
     */
    public function items()
    {
        $collection = Mage::getModel('customer/group')->getCollection();

        $result = [];
        foreach ($collection as $group) {
            /** @var Mage_Customer_Model_Group $group */
            $result[] = $group->toArray(['customer_group_id', 'customer_group_code']);
        }

        return $result;
    }
}
