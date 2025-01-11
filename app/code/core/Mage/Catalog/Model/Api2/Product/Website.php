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
 * Abstract Api2 model for product website resource
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Api2_Product_Website extends Mage_Api2_Model_Resource
{
    /**
     * Load product by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Catalog_Model_Product
     */
    protected function _loadProductById($id)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($id);
        if (!$product->getId()) {
            $this->_critical(sprintf('Product #%s not found.', $id), Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }
        return $product;
    }

    /**
     * Load website by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Core_Model_Website
     */
    protected function _loadWebsiteById($id)
    {
        /** @var Mage_Core_Model_Website $website */
        $website = Mage::getModel('core/website')->load($id);
        if (!$website->getId()) {
            $this->_critical(sprintf('Website #%s not found.', $id), Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }
        return $website;
    }
}
