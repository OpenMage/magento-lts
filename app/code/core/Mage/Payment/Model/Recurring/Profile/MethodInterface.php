<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Payment
 */

/**
 * Recurring profile gateway management interface
 *
 * @category   Mage
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
