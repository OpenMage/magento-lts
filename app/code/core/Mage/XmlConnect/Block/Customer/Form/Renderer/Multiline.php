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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer multi-line field form xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Form_Renderer_Multiline extends Enterprise_Customer_Block_Form_Renderer_Multiline
{
    /**
     * Field type
     *
     * @var string
     */
    protected $_filedType = 'multiline';

    /**
     * Add multiline field to fieldset xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $fieldsetXmlObj
     * @return Mage_XmlConnect_Block_Customer_Form_Renderer_Multiline
     */
    public function addFieldToXmlObj(Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $fieldsetXmlObj)
    {
        $attributes = array(
            'label' => $this->getLabel(),
            'name'  => $this->getFieldName(),
            'line_count'  => $this->getLineCount(),
            'attribute_code' => $this->getAttributeObject()->getAttributeCode(),
            'value' => $this->getValues()
        );

        $attributes += Mage::helper('xmlconnect/customer_form_renderer')
            ->addTitleAndRequiredAttr($fieldsetXmlObj, $this);

        $fieldXmlObj = $fieldsetXmlObj->addField(
            $this->_filedType . '_' . $this->getHtmlId(), $this->_filedType, $attributes
        );

        $validateRules = $this->getAttributeObject()->getValidateRules();

        if (!empty($validateRules)) {
            $validatorXmlObj = $fieldXmlObj->addValidator();

            if (!empty($validateRules['min_text_length'])) {
                $minTextLength = (int) $validateRules['min_text_length'];
                $validatorXmlObj->addRule(array('type' => 'min_length', 'value' => $minTextLength));
            }

            if (!empty($validateRules['max_text_length'])) {
                $maxTextLength = (int) $validateRules['max_text_length'];
                $validatorXmlObj->addRule(array('type' => 'max_length', 'value' => $maxTextLength));
            }

            if (!empty($validateRules['input_validation'])) {
                $validatorXmlObj->addRule(array('type' => $validateRules['input_validation']));
            }
        }

        return $this;
    }
}
