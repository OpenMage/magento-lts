<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tax Event Observer
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_Observer_AddTaxPercentToProductCollection implements Mage_Core_Observer_Interface
{
    /**
     * Add tax percent values to product collection items
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        $helper = Mage::helper('tax');
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = $observer->getEvent()->getDataByKey('collection');
        $store = $collection->getStoreId();
        if (!$helper->needPriceConversion($store)) {
            return $this;
        }

        if ($collection->requireTaxPercent()) {
            $request = Mage::getSingleton('tax/calculation')->getRateRequest();
            foreach ($collection as $item) {
                if ($item->getTaxClassId() === null) {
                    $item->setTaxClassId($item->getMinimalTaxClassId());
                }
                if (!isset($classToRate[$item->getTaxClassId()])) {
                    $request->setProductClassId($item->getTaxClassId());
                    $classToRate[$item->getTaxClassId()] = Mage::getSingleton('tax/calculation')->getRate($request);
                }
                $item->setTaxPercent($classToRate[$item->getTaxClassId()]);
            }
        }

        return $this;
    }
}
