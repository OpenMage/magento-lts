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
 * @package    Mage_Weee
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Update configurable options of the product view page
 *
 * @category   Mage
 * @package    Mage_Weee
 */
class Mage_Weee_Model_Observer_UpdateCofigurableProductOptions extends Mage_Core_Model_Abstract implements Mage_Core_Observer_Interface
{
    /**
     * Update configurable options of the product view page
     *
     * @throws Mage_Core_Exception
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        /** @var Mage_Weee_Helper_Data $weeeHelper */
        $weeeHelper = Mage::helper('weee');
        if (!$weeeHelper->isEnabled()) {
            return $this;
        }

        /** @var Varien_Object $response */
        $response = $observer->getEvent()->getDataByKey('response_object');
        $options = $response->getDataByKey('additional_options');

        /** @var Mage_Catalog_Model_Product|null $eventProduct */
        $eventProduct = $observer->getEvent()->getDataByKey('product');
        $_product = $eventProduct ?: Mage::registry('current_product');

        if (!$_product) {
            return $this;
        }

        $amount = $weeeHelper->getAmountForDisplay($_product);
        $origAmount = $weeeHelper->getOriginalAmount($_product);
        $attributes = $weeeHelper->getProductWeeeAttributes($_product, null, null, null, $weeeHelper->isTaxable());
        $amountInclTaxes = $weeeHelper->getAmountInclTaxes($attributes);
        $taxes = $amountInclTaxes - $amount;

        if ($weeeHelper->typeOfDisplay($_product, 3)) {
            // don't show weee as part of the product
            $options['oldPlusDisposition'] = 0;
            $options['plusDisposition'] = 0;
            $options['plusDispositionTax'] = 0;
        } else {
            $options['oldPlusDisposition'] = $origAmount;
            $options['plusDisposition'] = $amount;
            $options['plusDispositionTax'] = ($taxes < 0) ? 0 : $taxes;
        }

        // Exclude Weee amount from excluding tax amount
        if (!$weeeHelper->typeOfDisplay($_product, [0, 1, 4])) {
            $options['exclDisposition'] = true;
        }

        $response->setData('additional_options', $options);
        return $this;
    }
}
