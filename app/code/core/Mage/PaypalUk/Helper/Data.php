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
 * @category   Mage
 * @package    Mage_PaypalUk
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * PaypalUk data helper
 */
class Mage_PaypalUk_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Cross-models public exchange keys
     *
     * @var string
     */
    const CENTINEL_VPAS  = 'centinel_vpas_result';
    const CENTINEL_ECI   = 'centinel_eci_result';

    /**
     * All available payment info getter
     *
     * @param Mage_Payment_Model_Info $payment
     * @return array
     */
    public function getPaymentInfo(Mage_Payment_Model_Info $payment)
    {
        $result = array();

        $info = array(
            self::CENTINEL_VPAS,
            self::CENTINEL_ECI
        );

        foreach ($info as $key) {
            if ($value = $payment->getAdditionalInformation($key)) {
                $result[$this->_getLabel($key)] = $this->_getValue($value, $key);
            }
        }
        return $result;
    }

    /**
     * Render info item labels
     *
     * @param string $key
     */
    protected function _getLabel($key)
    {
        switch ($key) {
            case self::CENTINEL_VPAS:
                return $this->__('PayPal/Centinel Visa Payer Authentication Service Result');
            case self::CENTINEL_ECI:
                return $this->__('PayPal/Centinel Electronic Commerce Indicator');
        }
        return '';
    }

    /**
     * Apply a filter upon value getting
     *
     * @param string $value
     * @param string $key
     * @return string
     */
    protected function _getValue($value, $key)
    {
        switch ($key) {
            case self::CENTINEL_VPAS:
                return $this->_getCentinelVpasLabel($value);
            case self::CENTINEL_ECI:
                return $this->_getCentinelEciLabel($value);
        }
        return '';
    }

    /**
     * Attempt to convert centinel VPAS result into label
     *
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoDirectPayment
     * @param string $value
     * @return string
     */
    private function _getCentinelVpasLabel($value)
    {
        switch ($value) {
            case '2':
            case 'D':
                return $this->__('Authenticated, Good Result');
            case '1':
                return $this->__('Authenticated, Bad Result');
            case '3':
            case '6':
            case '8':
            case 'A':
            case 'C':
                return $this->__('Attempted Authentication, Good Result');
            case '4':
            case '7':
            case '9':
                return $this->__('Attempted Authentication, Bad Result');
            case '':
            case '0':
            case 'B':
                return $this->__('No Liability Shift');
            default:
                return $value;
        }
    }

    /**
     * Attempt to convert centinel ECI result into label
     *
     * @see https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoDirectPayment
     * @param string $value
     * @return string
     */
    private function _getCentinelEciLabel($value)
    {
        switch ($value) {
            case '01':
            case '07':
                return $this->__('Merchant Liability');
            case '02':
            case '05':
            case '06':
                return $this->__('Issuer Liability');
            default:
                return $value;
        }
    }
}
