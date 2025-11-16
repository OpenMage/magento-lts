<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Address format renderer default
 *
 * @package    Mage_Customer
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
     * @return $this
     */
    public function setType(Varien_Object $type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat(?Mage_Customer_Model_Address_Abstract $address = null)
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
     * @param null|string $format
     * @return string
     * @throws Exception
     */
    public function render(Mage_Customer_Model_Address_Abstract $address, $format = null)
    {
        $dataFormat = match ($this->getType()->getCode()) {
            'html' => Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_HTML,
            'pdf' => Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_PDF,
            'oneline' => Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_ONELINE,
            default => Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_TEXT,
        };

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
