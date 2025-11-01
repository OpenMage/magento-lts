<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Store API
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Store_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Retrieve stores list
     *
     * @return array
     */
    public function items()
    {
        // Retrieve stores
        $stores = Mage::app()->getStores();

        // Make result array
        $result = [];
        foreach ($stores as $store) {
            $result[] = [
                'store_id'    => $store->getId(),
                'code'        => $store->getCode(),
                'website_id'  => $store->getWebsiteId(),
                'group_id'    => $store->getGroupId(),
                'name'        => $store->getName(),
                'sort_order'  => $store->getSortOrder(),
                'is_active'   => $store->getIsActive(),
            ];
        }

        return $result;
    }

    /**
     * Retrieve store data
     *
     * @param int|string $storeId
     * @return array
     */
    public function info($storeId)
    {
        // Retrieve store info
        try {
            $store = Mage::app()->getStore($storeId);
        } catch (Mage_Core_Model_Store_Exception) {
            $this->_fault('store_not_exists');
        }

        if (!$store->getId()) {
            $this->_fault('store_not_exists');
        }

        // Basic store data
        $result = [];
        $result['store_id'] = $store->getId();
        $result['code'] = $store->getCode();
        $result['website_id'] = $store->getWebsiteId();
        $result['group_id'] = $store->getGroupId();
        $result['name'] = $store->getName();
        $result['sort_order'] = $store->getSortOrder();
        $result['is_active'] = $store->getIsActive();

        return $result;
    }
}
