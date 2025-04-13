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
class Mage_Tax_Model_Observer_PrepareCatalogIndexPriceSelect implements Mage_Core_Observer_Interface
{
    /**
     * Prepare select which is using to select index data for layered navigation
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        $table = $observer->getEvent()->getTable();
        $response = $observer->getEvent()->getResponseObject();

        $additionalCalculations = $response->getAdditionalCalculations();
        $calculation = Mage::helper('tax')->getPriceTaxSql(
            $table . '.min_price',
            $table . '.tax_class_id',
        );

        if (!empty($calculation)) {
            $additionalCalculations[] = $calculation;
            $response->setAdditionalCalculations($additionalCalculations);
        }

        return $this;
    }
}
