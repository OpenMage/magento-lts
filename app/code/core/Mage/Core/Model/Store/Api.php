<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Store API
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
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
                'is_active'   => $store->getIsActive()
            ];
        }

        return $result;
    }

    /**
     * Retrieve store data
     *
     * @param string|int $storeId
     * @return array
     */
    public function info($storeId)
    {
        // Retrieve store info
        try {
            $store = Mage::app()->getStore($storeId);
        } catch (Mage_Core_Model_Store_Exception $e) {
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
