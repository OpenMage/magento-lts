<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product options api
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Option_Api_V2 extends Mage_Catalog_Model_Product_Option_Api
{
    /**
     * Add custom option to product
     *
     * @param string $productId
     * @param array $data
     * @param int|string|null $store
     * @return bool
     */
    public function add($productId, $data, $store = null)
    {
        Mage::helper('api')->toArray($data);
        return parent::add($productId, $data, $store);
    }

    /**
     * Update product custom option data
     *
     * @param string $optionId
     * @param array $data
     * @param int|string|null $store
     * @return bool
     */
    public function update($optionId, $data, $store = null)
    {
        Mage::helper('api')->toArray($data);
        return parent::update($optionId, $data, $store);
    }

    /**
     * Retrieve list of product custom options
     *
     * @param string $productId
     * @param int|string|null $store
     * @return array
     */
    public function items($productId, $store = null)
    {
        $result = parent::items($productId, $store);
        foreach ($result as $key => $option) {
            $result[$key] = Mage::helper('api')->wsiArrayPacker($option);
        }
        return $result;
    }
}
