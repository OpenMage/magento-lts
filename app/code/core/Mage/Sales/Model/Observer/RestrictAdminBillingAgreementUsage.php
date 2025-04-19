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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Sales Model Observer
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Observer_RestrictAdminBillingAgreementUsage implements Mage_Core_Observer_Interface
{
    /**
     * Block admin ability to use customer billing agreements
     */
    public function execute(Varien_Event_Observer $observer): self
    {
        /** @var Mage_Payment_Model_Method_Abstract $methodInstance */
        $methodInstance = $observer->getEvent()->getDataByKey('method_instance');
        if (!($methodInstance instanceof Mage_Sales_Model_Payment_Method_Billing_AgreementAbstract)) {
            return $this;
        }
        if (!Mage::getSingleton('admin/session')->isAllowed('sales/billing_agreement/actions/use')) {
            /** @var stdClass $result */
            $result = $observer->getEvent()->getDataByKey('result');
            $result->isAvailable = false;
        }

        return $this;
    }
}
