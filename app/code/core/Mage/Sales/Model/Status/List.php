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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Service model for managing statuses information. Statuses are just records with code, message and any
 * additional data. The model helps to keep track and manipulate statuses, that different modules want to set
 * to owner object of this model.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Status_List
{
    /**
     * Status information entities
     *
     * @var array
     */
    protected $_items = [];

    /**
     * Adds status information to the list of items.
     *
     * @param string|null $origin Usually a name of module, that adds this status
     * @param int|null $code Code of status, unique for origin, that sets it
     * @param string|null $message Status message
     * @param Varien_Object|null $additionalData Any additional data, that caller would like to store
     * @return $this
     */
    public function addItem($origin = null, $code = null, $message = null, $additionalData = null)
    {
        $this->_items[] = [
            'origin' => $origin,
            'code' => $code,
            'message' => $message,
            'additionalData' => $additionalData
        ];
        return $this;
    }

    /**
     * Retrieves all items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Removes items, that have parameters equal to passed in $params.
     * Returns items removed.
     * $params can have following keys (if not set - then any item is good for this key):
     *   'origin', 'code', 'message'
     *
     * @param array $params
     * @return array
     */
    public function removeItemsByParams($params)
    {
        $items = $this->getItems();
        if (!$items) {
            return [];
        }

        $indexes = [];
        $paramKeys = ['origin', 'code', 'message'];
        foreach ($items as $index => $item) {
            $remove = true;
            foreach ($paramKeys as $key) {
                if (!isset($params[$key])) {
                    continue;
                }
                if ($params[$key] != $item[$key]) {
                    $remove = false;
                    break;
                }
            }
            if ($remove) {
                $indexes[] = $index;
            }
        }

        return $this->removeItems($indexes);
    }

    /**
     * Removes items at mentioned index/indexes.
     * Returns items removed.
     *
     * @param int|array $indexes
     * @return array
     */
    public function removeItems($indexes)
    {
        if (![$indexes]) {
            $indexes = [$indexes];
        }
        if (!$indexes) {
            return [];
        }

        $items = $this->getItems();
        if (!$items) {
            return [];
        }

        $newItems = [];
        $removedItems = [];
        foreach ($items as $indexNow => $item) {
            if (in_array($indexNow, $indexes)) {
                $removedItems[] = $item;
            } else {
                $newItems[] = $item;
            }
        }

        $this->_items = $newItems;
        return $removedItems;
    }

    /**
     * Clears list from all items
     *
     * @return $this
     */
    public function clear()
    {
        $this->_items = [];
        return $this;
    }
}
