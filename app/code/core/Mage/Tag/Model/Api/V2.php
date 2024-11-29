<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product Tag API
 *
 * @category   Mage
 * @package    Mage_Tag
 */
class Mage_Tag_Model_Api_V2 extends Mage_Tag_Model_Api
{
    /**
     * Retrieve list of tags for specified product as array of objects
     *
     * @param int $productId
     * @param string|int $store
     * @return array
     */
    public function items($productId, $store = null)
    {
        $result = parent::items($productId, $store);
        foreach ($result as $key => $tag) {
            $result[$key] = Mage::helper('api')->wsiArrayPacker($tag);
        }
        return array_values($result);
    }

    /**
     * Add tag(s) to product.
     * Return array of objects
     *
     * @param array $data
     * @return array
     */
    public function add($data)
    {
        $result = [];
        foreach (parent::add($data) as $key => $value) {
            $result[] = ['key' => $key, 'value' => $value];
        }

        return $result;
    }

    /**
     * Retrieve tag info as object
     *
     * @param int $tagId
     * @param string|int $store
     * @return object|array
     */
    public function info($tagId, $store)
    {
        $result = parent::info($tagId, $store);
        $result = Mage::helper('api')->wsiArrayPacker($result);
        foreach ($result->products as $key => $value) {
            $result->products[$key] = ['key' => $key, 'value' => $value];
        }
        return $result;
    }

    /**
     * Convert data from object to array before add
     *
     * @param array|object $data
     * @return array
     */
    protected function _prepareDataForAdd($data)
    {
        Mage::helper('api')->toArray($data);
        return parent::_prepareDataForAdd($data);
    }

    /**
     * Convert data from object to array before update
     *
     * @param array|object $data
     * @return array
     */
    protected function _prepareDataForUpdate($data)
    {
        Mage::helper('api')->toArray($data);
        return parent::_prepareDataForUpdate($data);
    }
}
