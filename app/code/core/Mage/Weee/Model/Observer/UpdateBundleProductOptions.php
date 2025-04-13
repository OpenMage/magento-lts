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
 * Shopping cart operation observer
 *
 * @category   Mage
 * @package    Mage_Weee
 */
class Mage_Weee_Model_Observer_UpdateBundleProductOptions extends Mage_Core_Model_Abstract implements Mage_Core_Observer_Interface
{
    /**
     * Process bundle options selection for prepare view json
     *
     * @throws Mage_Core_Exception
     */
    public function execute(Varien_Event_Observer $observer): void
    {
        /** @var Mage_Weee_Helper_Data $weeeHelper */
        $weeeHelper = Mage::helper('weee');
        if (!$weeeHelper->isEnabled()) {
            return;
        }

        /** @var Varien_Object $response */
        $response = $observer->getEvent()->getDataByKey('response_object');
        /** @var Mage_Catalog_Model_Product $selection */
        $selection = $observer->getEvent()->getDataByKey('selection');
        $options = $response->getDataByKey('additional_options');

        $_product = Mage::registry('current_product');

        $typeDynamic = Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Extend::DYNAMIC;
        if (!$_product || $_product->getPriceType() != $typeDynamic) {
            return;
        }

        $amount = $weeeHelper->getAmountForDisplay($selection);
        $attributes = $weeeHelper->getProductWeeeAttributes($_product, null, null, null, $weeeHelper->isTaxable());
        $amountInclTaxes = $weeeHelper->getAmountInclTaxes($attributes);
        $taxes = $amountInclTaxes - $amount;

        if ($weeeHelper->typeOfDisplay($_product, 3)) {
            // don't show weee as part of the product
            $options['plusDisposition'] = 0;
            $options['plusDispositionTax'] = 0;
        } else {
            $options['plusDisposition'] = $amount;
            $options['plusDispositionTax'] = ($taxes < 0) ? 0 : $taxes;
        }

        // Exclude Weee amount from excluding tax amount
        if (!$weeeHelper->typeOfDisplay($_product, [0, 1, 4])) {
            $options['exclDisposition'] = true;
        }

        $response->setData('additional_options', $options);
    }
}
