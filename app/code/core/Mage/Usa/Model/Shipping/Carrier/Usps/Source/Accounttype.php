<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS REST API Account Type Source Model
 *
 * Provides options for selecting the USPS account type used
 * for REST API pricing and label generation operations.
 *
 * Account Types:
 * - EPS: Enterprise Payment System - Most common for e-commerce
 * - PERMIT: Permit Reply Mail - For permit imprint accounts
 * - METER: Legacy postage meter accounts
 *
 * @category   Mage
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps_Source_Accounttype
{
    /**
     * EPS (Enterprise Payment System) account
     * Most common account type for e-commerce businesses
     */
    public const ACCOUNT_EPS = 'EPS';

    /**
     * Permit Reply Mail account
     * Requires permit ZIP code for payment authorization
     */
    public const ACCOUNT_PERMIT = 'PERMIT';

    /**
     * Postage Meter account type
     * Legacy option for businesses with physical postage meters
     */
    public const ACCOUNT_METER = 'METER';

    /**
     * Get option array for admin configuration dropdown
     *
     * @return array Array of options with 'value' and 'label' keys
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ACCOUNT_EPS,
                'label' => Mage::helper('usa')->__('EPS (Enterprise Payment System)'),
            ],
            [
                'value' => self::ACCOUNT_PERMIT,
                'label' => Mage::helper('usa')->__('Permit Reply Mail'),
            ],
            [
                'value' => self::ACCOUNT_METER,
                'label' => Mage::helper('usa')->__('Meter'),
            ],
        ];
    }

    /**
     * Get flat array of account type values
     *
     * @return array Array of account type constants
     */
    public function getAllOptions()
    {
        return [
            self::ACCOUNT_EPS,
            self::ACCOUNT_PERMIT,
            self::ACCOUNT_METER,
        ];
    }

    /**
     * Check if account type is valid
     *
     * @param string $accountType Account type to validate
     * @return bool True if valid account type
     */
    public function isValidAccountType($accountType)
    {
        return in_array($accountType, $this->getAllOptions(), true);
    }

    /**
     * Check if account type requires permit ZIP
     *
     * @param string $accountType Account type to check
     * @return bool True if permit ZIP is required
     */
    public function requiresPermitZip($accountType)
    {
        return $accountType === self::ACCOUNT_PERMIT;
    }
}
