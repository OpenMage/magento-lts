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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Locale_Config
{
    /**
     * List of allowed locales
     *
     * @var array
     */
    protected $_allowedLocales      = [
        'af_ZA' /*Afrikaans (South Africa)*/,   'ar_DZ' /*Arabic (Algeria)*/,       'ar_EG' /*Arabic (Egypt)*/,
        'ar_KW' /*Arabic (Kuwait)*/,            'ar_MA' /*Arabic (Morocco)*/,       'ar_SA' /*Arabic (Saudi Arabia)*/,
        'az_AZ' /*Azerbaijani (Azerbaijan)*/,   'be_BY' /*Belarusian (Belarus)*/,   'bg_BG' /*Bulgarian (Bulgaria)*/,
        'bn_BD' /*Bengali (Bangladesh)*/,       'bs_BA' /*Bosnian (Bosnia)*/,       'ca_ES' /*Catalan (Catalonia)*/,
        'cs_CZ' /*Czech (Czech Republic)*/,     'cy_GB' /*Welsh (United Kingdom)*/, 'da_DK' /*Danish (Denmark)*/,
        'de_AT' /*German (Austria)*/,           'de_CH' /*German (Switzerland)*/,   'de_DE' /*German (Germany)*/,
        'el_GR' /*Greek (Greece)*/,             'en_AU' /*English (Australian)*/,   'en_CA' /*English (Canadian)*/,
        'en_GB' /*English (United Kingdom)*/,   'en_NZ' /*English (New Zealand)*/,  'en_US' /*English (United States)*/,
        'es_AR' /*Spanish (Argentina)*/,        'es_CO' /*Spanish (Colombia)*/,     'es_PA' /*Spanish (Panama)*/,
        'gl_ES' /*Galician (Galician)*/,        'es_CR' /*Spanish (Costa Rica)*/,   'es_ES' /*Spanish (Spain)*/,
        'es_MX' /*Spanish (Mexico)*/,           'es_EU' /*Basque (Basque)*/,        'es_PE' /*Spanish (Peru)*/,
        'et_EE' /*Estonian (Estonia)*/,         'fa_IR' /*Persian (Iran)*/,         'fi_FI' /*Finnish (Finland)*/,
        'fil_PH' /*Filipino (Philippines)*/,    'fr_CA' /*French (Canada)*/,        'fr_FR' /*French (France)*/,
        'gu_IN' /*Gujarati (India)*/,           'he_IL' /*Hebrew (Israel)*/,        'hi_IN' /*Hindi (India)*/,
        'hr_HR' /*Croatian (Croatia)*/,         'hu_HU' /*Hungarian (Hungary)*/,    'id_ID' /*Indonesian (Indonesia)*/,
        'is_IS' /*Icelandic (Iceland)*/,        'it_CH' /*Italian (Switzerland)*/,  'it_IT' /*Italian (Italy)*/,
        'ja_JP' /*Japanese (Japan)*/,           'ka_GE' /*Georgian (Georgia)*/,     'km_KH' /*Khmer (Cambodia)*/,
        'ko_KR' /*Korean (South Korea)*/,       'lo_LA' /*Lao (Laos)*/,             'lt_LT' /*Lithuanian (Lithuania)*/,
        'lv_LV' /*Latvian (Latvia)*/,           'mk_MK' /*Macedonian (Macedonia)*/, 'mn_MN' /*Mongolian (Mongolia)*/,
        'ms_MY' /*Malaysian (Malaysia)*/,       'nl_NL' /*Dutch (Netherlands)*/,    'nb_NO' /*Norwegian BokmГ_l (Norway)*/,
        'nn_NO' /*Norwegian Nynorsk (Norway)*/, 'pl_PL' /*Polish (Poland)*/,        'pt_BR' /*Portuguese (Brazil)*/,
        'pt_PT' /*Portuguese (Portugal)*/,      'ro_RO' /*Romanian (Romania)*/,     'ru_RU' /*Russian (Russia)*/,
        'sk_SK' /*Slovak (Slovakia)*/,          'sl_SI' /*Slovenian (Slovenia)*/,   'sq_AL' /*Albanian (Albania)*/,
        'sr_RS' /*Serbian (Serbia)*/,           'sv_SE' /*Swedish (Sweden)*/,       'sw_KE' /*Swahili (Kenya)*/,
        'th_TH' /*Thai (Thailand)*/,            'tr_TR' /*Turkish (Turkey)*/,       'uk_UA' /*Ukrainian (Ukraine)*/,
        'vi_VN' /*Vietnamese (Vietnam)*/,       'zh_CN' /*Chinese (China)*/,        'zh_HK' /*Chinese (Hong Kong SAR)*/,
        'zh_TW' /*Chinese (Taiwan)*/,           'es_CL' /*Spanich (Chile)*/,        'lo_LA' /*Laotian*/,
        'es_VE' /*Spanish (Venezuela)*/,        'en_IE' /*English (Ireland)*/,
        'fr_CH' /*French (Switzerland)*/,
    ];

    /**
     * List of allowed currencies
     *
     * @var array
     */
    protected $_allowedCurrencies   = [
        'AFN' /*Afghani*/,          'ALL' /*Albanian Lek*/,     'AZN' /*Azerbaijanian Manat*/,      'DZD' /*Algerian Dinar*/,
        'AOA' /*Angolan Kwanza*/,   'ARS' /*Argentine Peso*/,   'AMD' /*Armenian Dram*/,            'AWG' /*Aruban Florin*/,
        'AUD' /*Australian Dollar*/,'BSD' /*Bahamian Dollar*/,  'BHD' /*Bahraini Dinar*/,           'BDT' /*Bangladesh Taka*/,
        'BBD' /*Barbados Dollar*/,  'BYR' /*Belarussian Ruble*/,'BZD' /*Belize Dollar*/,            'BMD' /*Bermudan Dollar*/,
        'BTN' /*Bhutan Ngultrum*/,  'BOB' /*Boliviano*/,        'BAM' /*Bosnia-Herzegovina Convertible Mark*/,'BWP' /*Botswanan Pula*/,
        'BRL' /*Brazilian Real*/,   'GBP' /*British Pound Sterling*/,'BND' /*Brunei Dollar*/,       'BGN' /*Bulgarian New Lev*/,
        'BUK' /*Burmese Kyat*/,     'BIF' /*Burundi Franc*/,    'KHR' /*Cambodian Riel*/,           'CAD' /*Canadian Dollar*/,
        'CVE' /*Cape Verde Escudo*/,'CZK' /*Czech Republic Koruna*/,'KYD' /*Cayman Islands Dollar*/,'GQE' /*Central African CFA Franc*/,
        'CLP' /*Chilean Peso*/,     'CNY' /*Chinese Yuan Renminbi*/,'COP' /*Colombian Peso*/,       'KMF' /*Comoro Franc*/,
        'CDF' /*Congolese Franc Congolais*/,'CRC' /*Costa Rican Colon*/,'HRK' /*Croatian Kuna*/,    'CUP' /*Cuban Peso*/,
        'DKK' /*Danish Krone*/,     'DJF' /*Djibouti Franc*/,   'DOP' /*Dominican Peso*/,           'XCD' /*East Caribbean Dollar*/,
        'EGP' /*Egyptian Pound*/,   'SVC' /*El Salvador Colon*/,'ERN' /*Eritrean Nakfa*/,           'EEK' /*Estonian Kroon*/,
        'ETB' /*Ethiopian Birr*/,   'EUR' /*Euro*/,             'FKP' /*Falkland Islands Pound*/,   'FJD' /*Fiji Dollar*/,
        'GMD' /*Gambia Dalasi*/,    'GEK' /*Georgian Kupon Larit*/,'GEL' /*Georgian Lari*/,         'GHS' /*Ghana Cedi*/,
        'GIP' /*Gibraltar Pound*/,  'GTQ' /*Guatemala Quetzal*/,'GNF' /*Guinea Franc*/,             'GYD' /*Guyana Dollar*/,
        'HTG' /*Haitian Gourde*/,   'HNL' /*Honduras Lempira*/, 'HKD' /*Hong Kong Dollar*/,         'HUF' /*Hungarian Forint*/,
        'ISK' /*Icelandic Krona*/,  'INR' /*Indian Rupee*/,     'IDR' /*Indonesian Rupiah*/,        'IRR' /*Iranian Rial*/,
        'IQD' /*Iraqi Dinar*/,      'ILS' /*Israeli New Sheqel*/,'JMD' /*Jamaican Dollar*/,         'JPY' /*Japanese Yen*/,
        'JOD' /*Jordanian Dinar*/,  'KZT' /*Kazakhstan Tenge*/, 'KES' /*Kenyan Shilling*/,          'KWD' /*Kuwaiti Dinar*/,
        'KGS' /*Kyrgystan Som*/,    'LAK' /*Laotian Kip*/,      'LVL' /*Latvian Lats*/,             'LBP' /*Lebanese Pound*/,
        'LSL' /*Lesotho Loti*/,     'LRD' /*Liberian Dollar*/,  'LYD' /*Libyan Dinar*/,             'LTL' /*Lithuanian Lita*/,
        'MOP' /*Macao Pataca*/,     'MKD' /*Macedonian Denar*/, 'MGA' /*Malagasy Ariary*/,          'MWK' /*Malawi Kwacha*/,
        'MYR' /*Malaysian Ringgit*/,'MVR' /*Maldive Islands Rufiyaa*/,'LSM' /*Maloti*/,             'MRO' /*Mauritania Ouguiya*/,
        'MUR' /*Mauritius Rupee*/,  'MXN' /*Mexican Peso*/,     'MDL' /*Moldovan Leu*/,             'MNT' /*Mongolian*/,
        'MAD' /*Moroccan Dirham*/,  'MZN' /*Mozambique Metical*/,'MMK' /*Myanmar Kyat*/,            'NAD' /*Namibia Dollar*/,
        'NPR' /*Nepalese Rupee*/,   'ANG' /*Netherlands Antillan Guilder*/,'YTL' /*New Turkish Lira*/,'NZD' /*New Zealand Dollar*/,
        'NIC' /*Nicaraguan Cordoba*/,'NGN' /*Nigerian Naira*/,  'KPW' /*North Korean Won*/,         'NOK' /*Norwegian Krone*/,
        'OMR' /*Oman Rial*/,        'PKR' /*Pakistan Rupee*/,   'PAB' /*Panamanian Balboa*/,        'PGK' /*Papua New Guinea Kina*/,
        'PYG' /*Paraguay Guarani*/, 'PEN' /*Peruvian Nuevo Sol*/,'PHP' /*Philippine Peso*/,         'PLN' /*Polish Zloty*/,
        'QAR' /*Qatari Rial*/,      'RHD' /*Rhodesian Dollar*/, 'RON' /*Romanian Leu*/,             'RUB' /*Russian Ruble*/,
        'RWF' /*Rwandan Franc*/,    'SHP' /*Saint Helena Pound*/,'STD' /*Sao Tome Dobra*/,          'SAR' /*Saudi Riyal*/,
        'RSD' /*Serbian Dinar*/,    'SCR' /*Seychelles Rupee*/, 'SLL' /*Sierra Leone Leone*/,       'SGD' /*Singapore Dollar*/,
        'SKK' /*Slovak Koruna*/,    'SBD' /*Solomon Islands Dollar*/,'SOS' /*Somali Shilling*/,     'ZAR' /*South African Rand*/,
        'KRW' /*South Korean Won*/, 'LKR' /*Sri Lanka Rupee*/,  'SDG' /*Sudanese Pound*/,           'SRD' /*Surinam Dollar*/,
        'SZL' /*Swaziland Lilangeni*/,'SEK' /*Swedish Krona*/,  'CHF' /*Swiss Franc*/,              'SYP' /*Syrian Pound*/,
        'TWD' /*Taiwan New Dollar*/,'TJS' /*Tajikistan Somoni*/,'TZS' /*Tanzanian Shilling*/,       'THB' /*Thai Baht*/,
        'TOP' /*Tonga Pa?anga*/,    'TTD' /*Trinidad and Tobago Dollar*/,'TND' /*Tunisian Dinar*/,  'TMM' /*Turkmenistan Manat*/,
        'USD' /*US Dollar*/,        'UGX' /*Ugandan Shilling*/, 'UAH' /*Ukrainian Hryvnia*/,        'AED' /*United Arab Emirates Dirham*/,
        'UYU' /*Uruguay Peso Uruguayo*/,'UZS' /*Uzbekistan Sum*/,'VUV' /*Vanuatu Vatu*/,            'VEB' /*Venezuelan Bolivar*/,
        'VEF' /*Venezuelan bolívar fuerte*/,'VND' /*Vietnamese Dong*/,  'CHE' /*WIR Euro*/,                'CHW' /*WIR Franc*/,
        'XOF' /*West African CFA franc*/,'WST' /*Western Samoa Tala*/,'YER' /*Yemeni Rial*/,        'ZMK' /*Zambian Kwacha*/,
        'ZWD' /*Zimbabwe Dollar*/,'TRY' /*Turkish Lira*/,'AZM' /*Azerbaijani Manat (1993-2006)*/,   'ROL' /*Old Romanian Leu*/,
        'TRL' /*Old Turkish Lira*/,'XPF' /*CFP Franc*/
    ];

    /**
     * Get list preconfigured allowed locales
     *
     * @return array
     */
    public function getAllowedLocales()
    {
        $configData = Mage::getConfig()->getNode(Mage_Core_Model_Locale::XML_PATH_ALLOW_CODES);
        if ($configData) {
            $configData = $configData->asArray();
        }
        if ($configData) {
            $configData = array_keys($configData);
        } else {
            $configData = [];
        }
        return array_merge($this->_allowedLocales, $configData);
    }

    /**
     * Get list preconfigured allowed currencies
     *
     * @return array
     */
    public function getAllowedCurrencies()
    {
        $configData = Mage::getConfig()->getNode(Mage_Core_Model_Locale::XML_PATH_ALLOW_CURRENCIES);
        if ($configData) {
            $configData = $configData->asArray();
        }
        if ($configData) {
            $configData = array_keys($configData);
        } else {
            $configData = [];
        }
        return array_merge($this->_allowedCurrencies, $configData);
    }
}
