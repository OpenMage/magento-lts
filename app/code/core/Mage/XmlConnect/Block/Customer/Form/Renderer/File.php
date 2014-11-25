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
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer file field form xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Form_Renderer_File extends Enterprise_Customer_Block_Form_Renderer_File
{
    /**
     * Field type
     *
     * @var string
     */
    protected $_filedType = 'file';

    /**
     * Change form post params for file upload compliance
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract $fieldsetXmlObj
     * @return Mage_XmlConnect_Block_Customer_Form_Renderer_File
     */
    protected function _setFormPostParams(Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract $fieldsetXmlObj)
    {
        $fieldsetXmlObj->getForm()->setData('method', 'post');
        $fieldsetXmlObj->getForm()->setData('enctype', 'multipart/form-data');
        return $this;
    }

    /**
     * Add file field to fieldset xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $fieldsetXmlObj
     * @return Mage_XmlConnect_Block_Customer_Form_Renderer_File
     */
    public function addFieldToXmlObj(Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $fieldsetXmlObj)
    {
        $this->_setFormPostParams($fieldsetXmlObj);

        $attributes = array(
            'label' => $this->getLabel(), 'name' => $this->getFieldName(), 'value' => $this->getEscapedValue()
        );

        $attributes += Mage::helper('xmlconnect/customer_form_renderer')
            ->addTitleAndRequiredAttr($fieldsetXmlObj, $this);

        $fieldXmlObj = $fieldsetXmlObj->addField($this->getHtmlId(), $this->_filedType, $attributes);
        $this->_addValidator($fieldXmlObj);

        return $this;
    }

    /**
     * Add validator for file field to fieldset xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract $fieldXmlObj
     * @return Mage_XmlConnect_Block_Customer_Form_Renderer_File
     */
    protected function _addValidator(Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract $fieldXmlObj)
    {
        $validateRules = $this->getAttributeObject()->getValidateRules();

        if (!empty($validateRules)) {
            $validatorXmlObj = $fieldXmlObj->addValidator();

            if (!empty($validateRules['max_file_size'])) {
                $minTextLength = (int) $validateRules['max_file_size'];
                $validatorXmlObj->addRule(array(
                    'type'          => 'max_file_size',
                    'value'         => $minTextLength,
                    'field_label'   => $this->getLabel()
                ));
            }

            if (!empty($validateRules['file_extensions'])) {
                $maxTextLength = $validateRules['file_extensions'];
                $validatorXmlObj->addRule(array(
                    'type'          => 'file_extensions',
                    'value'         => $maxTextLength,
                    'field_label'   => $this->getLabel()
                ));
            }
        }
        return $this;
    }
}
