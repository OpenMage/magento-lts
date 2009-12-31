<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal Currency Helper
 */
class Mage_Paypal_Helper_Currency extends Mage_Core_Helper_Abstract
{
    /**
     * Mapping array between alphabetical and numeric currency codes of ISO 4217
     *
     */
    protected $_isoMap = array(
        'AED' => '784',
        'AFN' => '971',
        'ALL' => '008',
        'AMD' => '051',
        'ANG' => '532',
        'AOA' => '973',
        'ARS' => '032',
        'AUD' => '036',
        'AWG' => '533',
        'AZN' => '944',
        'BAM' => '977',
        'BBD' => '052',
        'BDT' => '050',
        'BGN' => '975',
        'BHD' => '048',
        'BIF' => '108',
        'BMD' => '060',
        'BND' => '096',
        'BOB' => '068',
        'BOV' => '984',
        'BRL' => '986',
        'BSD' => '044',
        'BTN' => '064',
        'BWP' => '072',
        'BYR' => '974',
        'BZD' => '084',
        'CAD' => '124',
        'CDF' => '976',
        'CHE' => '947',
        'CHF' => '756',
        'CHW' => '948',
        'CLF' => '990',
        'CLP' => '152',
        'CNY' => '156',
        'COP' => '170',
        'COU' => '970',
        'CRC' => '188',
        'CUC' => '931',
        'CUP' => '192',
        'CVE' => '132',
        'CZK' => '203',
        'DJF' => '262',
        'DKK' => '208',
        'DOP' => '214',
        'DZD' => '012',
        'EEK' => '233',
        'EGP' => '818',
        'ERN' => '232',
        'ETB' => '230',
        'EUR' => '978',
        'FJD' => '242',
        'FKP' => '238',
        'GBP' => '826',
        'GEL' => '981',
        'GHS' => '936',
        'GIP' => '292',
        'GMD' => '270',
        'GNF' => '324',
        'GTQ' => '320',
        'GYD' => '328',
        'HKD' => '344',
        'HNL' => '340',
        'HRK' => '191',
        'HTG' => '332',
        'HUF' => '348',
        'IDR' => '360',
        'ILS' => '376',
        'INR' => '356',
        'IQD' => '368',
        'IRR' => '364',
        'ISK' => '352',
        'JMD' => '388',
        'JOD' => '400',
        'JPY' => '392',
        'KES' => '404',
        'KGS' => '417',
        'KHR' => '116',
        'KMF' => '174',
        'KPW' => '408',
        'KRW' => '410',
        'KWD' => '414',
        'KYD' => '136',
        'KZT' => '398',
        'LAK' => '418',
        'LBP' => '422',
        'LKR' => '144',
        'LRD' => '430',
        'LSL' => '426',
        'LTL' => '440',
        'LVL' => '428',
        'LYD' => '434',
        'MAD' => '504',
        'MDL' => '498',
        'MGA' => '969',
        'MKD' => '807',
        'MMK' => '104',
        'MNT' => '496',
        'MOP' => '446',
        'MRO' => '478',
        'MUR' => '480',
        'MVR' => '462',
        'MWK' => '454',
        'MXN' => '484',
        'MXV' => '979',
        'MYR' => '458',
        'MZN' => '943',
        'NAD' => '516',
        'NGN' => '566',
        'NIO' => '558',
        'NOK' => '578',
        'NPR' => '524',
        'NZD' => '554',
        'OMR' => '512',
        'PAB' => '590',
        'PEN' => '604',
        'PGK' => '598',
        'PHP' => '608',
        'PKR' => '586',
        'PLN' => '985',
        'PYG' => '600',
        'QAR' => '634',
        'RON' => '946',
        'RSD' => '941',
        'RUB' => '643',
        'RWF' => '646',
        'SAR' => '682',
        'SBD' => '090',
        'SCR' => '690',
        'SDG' => '938',
        'SEK' => '752',
        'SGD' => '702',
        'SHP' => '654',
        'SLL' => '694',
        'SOS' => '706',
        'SRD' => '968',
        'STD' => '678',
        'SYP' => '760',
        'SZL' => '748',
        'THB' => '764',
        'TJS' => '972',
        'TMT' => '934',
        'TND' => '788',
        'TOP' => '776',
        'TRY' => '949',
        'TTD' => '780',
        'TWD' => '901',
        'TZS' => '834',
        'UAH' => '980',
        'UGX' => '800',
        'USD' => '840',
        'USN' => '997',
        'USS' => '998',
        'UYU' => '858',
        'UZS' => '860',
        'VEF' => '937',
        'VND' => '704',
        'VUV' => '548',
        'WST' => '882',
        'XAF' => '950',
        'XAG' => '961',
        'XAU' => '959',
        'XBA' => '955',
        'XBB' => '956',
        'XBC' => '957',
        'XBD' => '958',
        'XCD' => '951',
        'XDR' => '960',
        'XOF' => '952',
        'XPD' => '964',
        'XPF' => '953',
        'XPT' => '962',
        'XTS' => '963',
        'XXX' => '999',
        'YER' => '886',
        'ZAR' => '710',
        'ZMK' => '894',
        'ZWL' => '932',
    );

    /**
     * By using mapping array get numeric code by alphabetical code
     *
     * @param $code string
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getIso4217CurrencyCode($code)
    {
        if (!empty($this->_isoMap[$code])) {
            return $this->_isoMap[$code];
        }
        Mage::throwException(Mage::helper('paypal')->__('Not supported currency code %s.', $code));
    }
}
