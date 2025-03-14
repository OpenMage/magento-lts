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
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Observer
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Observer_SingleSearchResult
{
    /**
     * Product list block name in layout
     */
    public const RESULT_BLOCK_NAME = 'search_result_list';

    /**
     * Disable Advanced Search at storeview scope
     */
    public function execute(Varien_Event_Observer $observer): void
    {
        /** @var Mage_Catalog_Helper_Search $helper */
        $helper = Mage::helper('catalog/search');

        if (!$helper->isRedirectSingleSearchResult()) {
            return;
        }

        /** @var Mage_Catalog_Block_Product_List $block */
        $block = Mage::app()->getLayout()->getBlock(self::RESULT_BLOCK_NAME);
        if ($block) {
            $collection = $block->getLoadedProductCollection();
            if ($collection && $collection->getSize() === 1) {
                /** @var Mage_Catalog_Model_Product $product */
                $product = $collection->getFirstItem();
                $url = $product->getProductUrl();
                if ($url) {
                    Mage::app()->getResponse()->setRedirect($url)->sendResponse();
                    exit; //stop everything else
                }
            }
        }
    }
}
