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
 * @package    Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review Observer Model
 *
 * @category   Mage
 * @package    Mage_Review
 */
class Mage_Review_Model_Observer_CatalogBlockProductCollectionBeforeToHtml implements Mage_Core_Observer_Interface
{
    /**
     * Append review summary before rendering html
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = $observer->getEvent()->getDataByKey('collection');
        if ($collection instanceof Varien_Data_Collection) {
            $collection->load();
            Mage::getModel('review/review')->appendSummary($collection);
        }

        return $this;
    }
}
