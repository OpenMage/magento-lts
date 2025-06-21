<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

class Mage_Paypal_Model_System_Config_Source_MerchantCountry
{
    /**
     * Get list of allowed merchant countries
     * https://developer.paypal.com/docs/platforms/seller-onboarding/before-you-begin/#supported-countries
     *
     * @return array
     */
    public function toOptionArray()
    {
        $countries = Mage::getSingleton('adminhtml/system_config_source_country')
            ->toOptionArray(false);

        // Filter to only PayPal supported countries
        $supportedCountries = [
            'AU', // Australia
            'AT', // Austria
            'BE', // Belgium
            'BR', // Brazil
            'CA', // Canada
            'CN', // China
            'CZ', // Czech Republic
            'DK', // Denmark
            'FI', // Finland
            'FR', // France
            'DE', // Germany
            'GR', // Greece
            'HK', // Hong Kong
            'HU', // Hungary
            'IN', // India
            'ID', // Indonesia
            'IE', // Ireland
            'IL', // Israel
            'IT', // Italy
            'JP', // Japan
            'LU', // Luxembourg
            'MY', // Malaysia
            'MX', // Mexico
            'NL', // Netherlands
            'NZ', // New Zealand
            'NO', // Norway
            'PH', // Philippines
            'PL', // Poland
            'PT', // Portugal
            'RU', // Russia
            'SG', // Singapore
            'SK', // Slovakia
            'KR', // South Korea
            'ES', // Spain
            'SE', // Sweden
            'CH', // Switzerland
            'TW', // Taiwan
            'TH', // Thailand
            'TR', // Turkey
            'AE', // United Arab Emirates
            'GB', // United Kingdom
            'US', // United States
            'VN', // Vietnam
        ];

        return array_filter($countries, function ($country) use ($supportedCountries) {
            return in_array($country['value'], $supportedCountries);
        });
    }
}
