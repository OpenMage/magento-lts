<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Address format renderer default
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Block_Address_Renderer_Default extends Mage_Core_Block_Abstract implements Mage_Customer_Block_Address_Renderer_Interface
{
    /**
     * Format type object
     *
     * @var Varien_Object
     */
    protected $_type;

    /**
     * Retrieve format type object
     *
     * @return Varien_Object
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Retrieve format type object
     *
     * @param  Varien_Object $type
     * @return $this
     */
    public function setType(Varien_Object $type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * @param Mage_Customer_Model_Address_Abstract|null $address
     * @return string
     */
    public function getFormat(Mage_Customer_Model_Address_Abstract $address = null)
    {
        $countryFormat = is_null($address)
            ? false
            : $address->getCountryModel()->getFormat($this->getType()->getCode());
        if ($countryFormat) {
            $format = $countryFormat->getFormat();
        } else {
            $regExp = "/^[^()\n]*+(\((?>[^()\n]|(?1))*+\)[^()\n]*+)++$|^[^()]+?$/m";
            preg_match_all($regExp, $this->getType()->getDefaultFormat(), $matches, PREG_SET_ORDER);
            $format = count($matches) ? $this->_prepareAddressTemplateData($this->getType()->getDefaultFormat()) : null;
        }
        return $format;
    }

    /**
     * Render address
     *
     * @param Mage_Customer_Model_Address_Abstract $address
     * @param string|null $format
     * @return string
     * @throws Exception
     */
    public function render(Mage_Customer_Model_Address_Abstract $address, $format = null)
    {
        switch ($this->getType()->getCode()) {
            case 'html':
                $dataFormat = Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_HTML;
                break;
            case 'pdf':
                $dataFormat = Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_PDF;
                break;
            case 'oneline':
                $dataFormat = Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_ONELINE;
                break;
            default:
                $dataFormat = Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_TEXT;
                break;
        }

        $formater   = new Varien_Filter_Template();
        $attributes = Mage::helper('customer/address')->getAttributes();

        $data = [];
        foreach ($attributes as $attribute) {
            /** @var Mage_Customer_Model_Attribute $attribute */
            if (!$attribute->getIsVisible()) {
                continue;
            }
            if ($attribute->getAttributeCode() == 'country_id') {
                $data['country'] = $address->getCountryModel()->getName();
            } elseif ($attribute->getAttributeCode() == 'region') {
                $data['region'] = Mage::helper('directory')->__($address->getRegion());
            } else {
                $dataModel = Mage_Customer_Model_Attribute_Data::factory($attribute, $address);
                $value     = $dataModel->outputValue($dataFormat);
                if ($attribute->getFrontendInput() == 'multiline') {
                    $values    = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_ARRAY);
                    // explode lines
                    foreach ($values as $k => $v) {
                        $key = sprintf('%s%d', $attribute->getAttributeCode(), $k + 1);
                        $data[$key] = $v;
                    }
                }
                $data[$attribute->getAttributeCode()] = $value;
            }
        }

        if ($this->getType()->getHtmlEscape()) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->escapeHtml($value);
            }
        }

        $formater->setVariables($data);
        $format = !is_null($format) ? $format : $this->_prepareAddressTemplateData($this->getFormat($address));

        return $formater->filter($format);
    }

    /**
     * Get address template data without url and js code
     * @param string $data
     * @return string
     */
    protected function _prepareAddressTemplateData($data)
    {
        $result = '';
        if (is_string($data)) {
            $urlRegExp = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@";
            /** @var Mage_Core_Model_Input_Filter_MaliciousCode $maliciousCodeFilter */
            $maliciousCodeFilter = Mage::getSingleton('core/input_filter_maliciousCode');
            $result = preg_replace($urlRegExp, ' ', $maliciousCodeFilter->filter($data));
        }
        return $result;
    }
}
