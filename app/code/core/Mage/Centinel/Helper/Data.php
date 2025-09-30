<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Centinel
 */

/**
 * Centinel module base helper
 *
 * @package    Mage_Centinel
 */
class Mage_Centinel_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Centinel';

    /**
     * Return label for cmpi field
     *
     * @param string $fieldName
     * @return string
     */
    public function getCmpiLabel($fieldName)
    {
        return match ($fieldName) {
            Mage_Centinel_Model_Service::CMPI_PARES => $this->__('3D Secure Verification Result'),
            Mage_Centinel_Model_Service::CMPI_ENROLLED => $this->__('3D Secure Cardholder Validation'),
            Mage_Centinel_Model_Service::CMPI_ECI => $this->__('3D Secure Electronic Commerce Indicator'),
            Mage_Centinel_Model_Service::CMPI_CAVV => $this->__('3D Secure CAVV'),
            Mage_Centinel_Model_Service::CMPI_XID => $this->__('3D Secure XID'),
            default => '',
        };
    }

    /**
     * Return value for cmpi field
     *
     * @param string $fieldName
     * @param string $value
     * @return string
     */
    public function getCmpiValue($fieldName, $value)
    {
        return match ($fieldName) {
            Mage_Centinel_Model_Service::CMPI_PARES => $this->_getCmpiParesValue($value),
            Mage_Centinel_Model_Service::CMPI_ENROLLED => $this->_getCmpiEnrolledValue($value),
            Mage_Centinel_Model_Service::CMPI_ECI => $this->_getCmpiEciValue($value),
            Mage_Centinel_Model_Service::CMPI_CAVV, Mage_Centinel_Model_Service::CMPI_XID => $value,
            default => '',
        };
    }

    /**
     * Return text value for cmpi eci flag field
     *
     * @param string $value
     * @return string
     */
    private function _getCmpiEciValue($value)
    {
        return match ($value) {
            '01', '07' => $this->__('Merchant Liability'),
            '02', '05', '06' => $this->__('Card Issuer Liability'),
            default => $value,
        };
    }

    /**
     * Return text value for cmpi enrolled field
     *
     * @param string $value
     * @return string
     */
    private function _getCmpiEnrolledValue($value)
    {
        return match ($value) {
            'Y' => $this->__('Enrolled'),
            'U' => $this->__('Enrolled but Authentication Unavailable'),
            default => $this->__('Not Enrolled'),
        };
    }

    /**
     * Return text value for cmpi pares field
     *
     * @param string $value
     * @return string
     */
    private function _getCmpiParesValue($value)
    {
        return match ($value) {
            'Y' => $this->__('Successful'),
            'N' => $this->__('Failed'),
            'U' => $this->__('Unable to complete'),
            'A' => $this->__('Successful attempt'),
            default => $value,
        };
    }

    /**
     * Return centinel block for payment form with logos
     *
     * @param Mage_Payment_Model_Method_Abstract $method
     * @return Mage_Centinel_Block_Logo|Mage_Core_Block_Abstract|false
     */
    public function getMethodFormBlock($method)
    {
        $blockType = 'centinel/logo';
        if ($this->getLayout()) {
            $block = $this->getLayout()->createBlock($blockType);
        } else {
            $className = Mage::getConfig()->getBlockClassName($blockType);
            $block = new $className();
        }
        $block->setMethod($method);
        return $block;
    }

    /**
     * Return url of page about visa verification
     *
     * @return string
     */
    public function getVisaLearnMorePageUrl()
    {
        return 'https://usa.visa.com/personal/security/vbv/index.html?ep=v_sym_verifiedbyvisa';
    }

    /**
     * Return url of page about mastercard verification
     *
     * @return string
     */
    public function getMastercardLearnMorePageUrl()
    {
        return 'http://www.mastercardbusiness.com/mcbiz/index.jsp?template=/orphans&amp;content=securecodepopup';
    }
}
