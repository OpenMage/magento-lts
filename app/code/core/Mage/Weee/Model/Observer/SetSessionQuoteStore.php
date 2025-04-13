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
class Mage_Weee_Model_Observer_SetSessionQuoteStore extends Mage_Core_Model_Abstract implements Mage_Core_Observer_Interface
{
    /**
     * Notify weee helper about the admin session quote store when creating order
     * in the backend
     */
    public function execute(Varien_Event_Observer $observer): void
    {
        /** @var Mage_Weee_Helper_Data $weeeHelper */
        $weeeHelper = Mage::helper('weee');

        /** @var Mage_Adminhtml_Model_Session_Quote $sessionQuote */
        $sessionQuote = $observer->getEvent()->getDataByKey('session_quote');
        if ($sessionQuote) {
            $weeeHelper->setStore($sessionQuote->getStore());
        }
    }
}
