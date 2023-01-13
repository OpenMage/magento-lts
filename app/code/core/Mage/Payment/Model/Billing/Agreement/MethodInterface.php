<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Interface for payment methods that support billing agreements management
 *
 * @category   Mage
 * @package    Mage_Payment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Payment_Model_Billing_Agreement_MethodInterface
{
    /**
     * Init billing agreement
     *
     * @param Mage_Payment_Model_Billing_AgreementAbstract $agreement
     */
    public function initBillingAgreementToken(Mage_Payment_Model_Billing_AgreementAbstract $agreement);

    /**
     * Retrieve billing agreement details
     *
     * @param Mage_Payment_Model_Billing_AgreementAbstract $agreement
     */
    public function getBillingAgreementTokenInfo(Mage_Payment_Model_Billing_AgreementAbstract $agreement);

    /**
     * Create billing agreement
     *
     * @param Mage_Payment_Model_Billing_AgreementAbstract $agreement
     */
    public function placeBillingAgreement(Mage_Payment_Model_Billing_AgreementAbstract $agreement);

    /**
     * Update billing agreement status
     *
     * @param Mage_Payment_Model_Billing_AgreementAbstract $agreement
     */
    public function updateBillingAgreementStatus(Mage_Payment_Model_Billing_AgreementAbstract $agreement);
}
