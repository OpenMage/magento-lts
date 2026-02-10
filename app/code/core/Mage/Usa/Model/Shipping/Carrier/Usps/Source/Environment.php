<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Usa
 */

/**
 * USPS REST API Environment source model
 *
 * Provides options for selecting between Production and Sandbox (Test) environments.
 *
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Usps_Source_Environment
{
    /**
     * Production environment
     */
    public const ENV_PRODUCTION = 'production';

    /**
     * Sandbox (Test) environment
     */
    public const ENV_SANDBOX = 'sandbox';

    /**
     * Production REST API URL
     */
    public const URL_PRODUCTION = 'https://apis.usps.com/';

    /**
     * Sandbox REST API URL
     */
    public const URL_SANDBOX = 'https://apis-tem.usps.com/';

    /**
     * Get option array for admin configuration
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENV_PRODUCTION,
                'label' => Mage::helper('usa')->__('Production (Live)'),
            ],
            [
                'value' => self::ENV_SANDBOX,
                'label' => Mage::helper('usa')->__('Sandbox (Testing)'),
            ],
        ];
    }

    /**
     * Get REST API URL for given environment
     *
     * @param string $environment
     * @return string
     */
    public function getUrlForEnvironment($environment)
    {
        switch ($environment) {
            case self::ENV_PRODUCTION:
                return self::URL_PRODUCTION;
            case self::ENV_SANDBOX:
                return self::URL_SANDBOX;
            default:
                // Default to production for safety
                return self::URL_PRODUCTION;
        }
    }
}
