<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Credit memo API
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Order_Creditmemo_Api_V2 extends Mage_Sales_Model_Order_Creditmemo_Api
{
    /**
     * Prepare filters
     *
     * @deprecated since 1.7.0.1
     * @param null|object $filters
     * @return array
     */
    protected function _prepareListFilter($filters = null)
    {
        $preparedFilters = [];
        $helper = Mage::helper('api');
        if (isset($filters->filter)) {
            $helper->associativeArrayUnpack($filters->filter);
            $preparedFilters += $filters->filter;
        }
        if (isset($filters->complex_filter)) {
            $helper->associativeArrayUnpack($filters->complex_filter);
            foreach ($filters->complex_filter as &$filter) {
                $helper->associativeArrayUnpack($filter);
            }
            $preparedFilters += $filters->complex_filter;
        }
        foreach ($preparedFilters as $field => $value) {
            if (isset($this->_attributesMap['creditmemo'][$field])) {
                $preparedFilters[$this->_attributesMap['creditmemo'][$field]] = $value;
                unset($preparedFilters[$field]);
            }
        }

        return $preparedFilters;
    }

    /**
     * Prepare data
     *
     * @param null|object $data
     * @return array
     */
    protected function _prepareCreateData($data)
    {
        // convert data object to array, if it's null turn it into empty array
        $data = (isset($data) && is_object($data)) ? get_object_vars($data) : [];
        // convert qtys object to array
        if (isset($data['qtys']) && count($data['qtys'])) {
            $qtysArray = [];
            foreach ($data['qtys'] as &$item) {
                if (isset($item->order_item_id) && isset($item->qty)) {
                    $qtysArray[$item->order_item_id] = $item->qty;
                }
            }
            $data['qtys'] = $qtysArray;
        }
        return $data;
    }
}
