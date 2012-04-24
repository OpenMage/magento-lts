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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer date field form xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Form_Renderer_Date extends Enterprise_Customer_Block_Form_Renderer_Date
{
    /**
     * Field type
     *
     * @var string
     */
    protected $_filedType = 'date';

    /**
     * Prepare values for renderer
     *
     * @return array
     */
    protected function _prepareValues()
    {
        return array('day' => array(
            'id' => $this->getHtmlId('day'),
            'title' => $this->__('Day'),
            'label' => $this->__('DD'),
            'value' => $this->getDay()
        ), 'month' => array(
            'id' => $this->getHtmlId('month'),
            'title' => $this->__('Month'),
            'label' => $this->__('MM'),
            'value' => $this->getMonth()
        ), 'year' => array(
            'id' => $this->getHtmlId('year'),
            'title' => $this->__('Year'),
            'label' => $this->__('YYYY'),
            'value' => $this->getYear()
        ));
    }

    /**
     * Add date field to fieldset xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $fieldsetXmlObj
     * @return Mage_XmlConnect_Block_Customer_Form_Renderer_Date
     */
    public function addFieldToXmlObj(Mage_XmlConnect_Model_Simplexml_Form_Element_Fieldset $fieldsetXmlObj)
    {
        $attributes = array(
            'label' => $this->getLabel(),
            'name'  => $this->getFieldName(),
            'format'=> $this->getDateFormat(),
            'value' => $this->_prepareValues()
        );

        $attributes += Mage::helper('xmlconnect/customer_form_renderer')
            ->addTitleAndRequiredAttr($fieldsetXmlObj, $this);
        $fieldXmlObj = $fieldsetXmlObj->addField($this->getHtmlId('full'), $this->_filedType, $attributes);
        $validateRules = $this->getAttributeObject()->getValidateRules();

        if (!empty($validateRules)) {
            $validatorXmlObj = $fieldXmlObj->addValidator();
            if (!empty($validateRules['input_validation'])) {
                $validatorXmlObj->addRule(array('type' => $validateRules['input_validation']));
            }
        }

        return $this;
    }
}
