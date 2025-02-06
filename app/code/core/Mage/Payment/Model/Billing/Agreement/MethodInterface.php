<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Payment
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
