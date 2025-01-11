<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Interface for payment methods that support billing agreements management
 *
 * @category   Mage
 * @package    Mage_Payment
 */
interface Mage_Payment_Model_Billing_Agreement_MethodInterface
{
    /**
     * Init billing agreement
     */
    public function initBillingAgreementToken(Mage_Payment_Model_Billing_AgreementAbstract $agreement);

    /**
     * Retrieve billing agreement details
     */
    public function getBillingAgreementTokenInfo(Mage_Payment_Model_Billing_AgreementAbstract $agreement);

    /**
     * Create billing agreement
     */
    public function placeBillingAgreement(Mage_Payment_Model_Billing_AgreementAbstract $agreement);

    /**
     * Update billing agreement status
     */
    public function updateBillingAgreementStatus(Mage_Payment_Model_Billing_AgreementAbstract $agreement);
}
