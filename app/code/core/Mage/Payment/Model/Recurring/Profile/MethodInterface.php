<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Recurring profile gateway management interface
 *
 * @package    Mage_Payment
 */
interface Mage_Payment_Model_Recurring_Profile_MethodInterface
{
    /**
     * Validate data
     *
     * @throws Mage_Core_Exception
     */
    public function validateRecurringProfile(Mage_Payment_Model_Recurring_Profile $profile);

    /**
     * Submit to the gateway
     */
    public function submitRecurringProfile(Mage_Payment_Model_Recurring_Profile $profile, Mage_Payment_Model_Info $paymentInfo);

    /**
     * Fetch details
     *
     * @param string $referenceId
     */
    public function getRecurringProfileDetails($referenceId, Varien_Object $result);

    /**
     * Check whether can get recurring profile details
     *
     * @return bool
     */
    public function canGetRecurringProfileDetails();

    /**
     * Update data
     */
    public function updateRecurringProfile(Mage_Payment_Model_Recurring_Profile $profile);

    /**
     * Manage status
     */
    public function updateRecurringProfileStatus(Mage_Payment_Model_Recurring_Profile $profile);
}
