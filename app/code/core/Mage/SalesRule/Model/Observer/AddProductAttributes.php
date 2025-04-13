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
 * @package    Mage_SalesRule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * SalesRule Model Observer
 *
 * @category   Mage
 * @package    Mage_SalesRule
 */
class Mage_SalesRule_Model_Observer_AddProductAttributes implements Mage_Core_Observer_Interface
{
    /**
     * Append sales rule product attributes to select by quote item collection
     *
     * @throws Mage_Core_Exception
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        /** @var Varien_Object $attributesTransfer */
        $attributesTransfer = $observer->getEvent()->getDataByKey('attributes');

        $attributes = Mage::getResourceModel('salesrule/rule')
            ->getActiveAttributes(
                Mage::app()->getWebsite()->getId(),
                Mage::getSingleton('customer/session')->getCustomer()->getGroupId(),
            );
        $result = [];
        foreach ($attributes as $attribute) {
            $result[$attribute['attribute_code']] = true;
        }
        $attributesTransfer->addData($result);
        return $this;
    }
}
