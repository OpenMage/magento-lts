<?php

declare(strict_types=1);
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
     * https://developer.paypal.com/docs/multiparty/seller-onboarding/#country-eligibility
     */
    public function toOptionArray(): array
    {
        $countries = Mage::getSingleton('adminhtml/system_config_source_country')
            ->toOptionArray();

        $supportedCountries = [
            'AL',
            'DZ',
            'AD',
            'AG',
            'AR',
            'AU',
            'AT',
            'BS',
            'BH',
            'BB',
            'BE',
            'BZ',
            'BA',
            'BW',
            'BR',
            'BG',
            'CA',
            'CL',
            'CN',
            'CR',
            'HR',
            'CY',
            'CZ',
            'DK',
            'DO',
            'EC',
            'EG',
            'SV',
            'EE',
            'FI',
            'FR',
            'DE',
            'GR',
            'GT',
            'HN',
            'HK',
            'HU',
            'IS',
            'IN',
            'ID',
            'IE',
            'IT',
            'JM',
            'JP',
            'JO',
            'KE',
            'KW',
            'LV',
            'LI',
            'LT',
            'LU',
            'MY',
            'MT',
            'MX',
            'MD',
            'MC',
            'NL',
            'NZ',
            'NO',
            'OM',
            'PA',
            'PE',
            'PH',
            'PL',
            'PT',
            'QA',
            'RO',
            'RU',
            'SA',
            'RS',
            'SG',
            'SK',
            'SI',
            'ZA',
            'KR',
            'ES',
            'SE',
            'CH',
            'TW',
            'TH',
            'TT',
            'AE',
            'GB',
            'US',
            'UY',
            'VN',
        ];

        return array_filter($countries, function ($country) use ($supportedCountries) {
            return in_array($country['value'], $supportedCountries);
        });
    }
}
