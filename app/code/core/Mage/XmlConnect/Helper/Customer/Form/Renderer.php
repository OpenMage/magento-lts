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
 * Customer form renderer helper
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Helper_Customer_Form_Renderer extends Mage_Core_Helper_Abstract
{
    /**
     * Attributes block name
     *
     * @var string
     */
    protected $_attributesBlockName;

    /**
     * Form code
     *
     * @var string
     */
    protected $_formCode;

    /**
     * Set block entity
     *
     * @var object
     */
    protected $_blockEntity;

    /**
     * Block entity type
     *
     * @var null|string
     */
    protected $_blockEntityType;

    /**
     * Enterprise field renderer list as type => renderer block
     *
     * Original block relations:
     * - 'text'      => 'enterprise_customer/form_renderer_text',
     * - 'textarea'  => 'enterprise_customer/form_renderer_textarea',
     * - 'multiline' => 'enterprise_customer/form_renderer_multiline',
     * - 'date'      => 'enterprise_customer/form_renderer_date',
     * - 'select'    => 'enterprise_customer/form_renderer_select',
     * - 'multiselect' => 'enterprise_customer/form_renderer_multiselect',
     * - 'boolean'   => 'enterprise_customer/form_renderer_boolean',
     * - 'file'     => 'enterprise_customer/form_renderer_file'
     * - 'image'     => 'enterprise_customer/form_renderer_image'
     *
     * @see customer.xml layout customer_form_template_handle node
     * @var array
     */
    protected $_customerFiledRenderer = array(
        'text' => 'xmlconnect/customer_form_renderer_text',
        'textarea' => 'xmlconnect/customer_form_renderer_textarea',
        'multiline' => 'xmlconnect/customer_form_renderer_multiline',
        'date' => 'xmlconnect/customer_form_renderer_date',
        'select' => 'xmlconnect/customer_form_renderer_select',
        'multiselect' => 'xmlconnect/customer_form_renderer_multiselect',
        'boolean' => 'xmlconnect/customer_form_renderer_boolean',
        'file' => 'xmlconnect/customer_form_renderer_file',
        'image' => 'xmlconnect/customer_form_renderer_image'
    );

    /**
     * Add custom attributes
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Abstract $fieldset
     * @param Mage_Core_Model_Layout $layout
     * @return Mage_XmlConnect_Helper_Customer_Form_Renderer
     */
    public function addCustomAttributes(Mage_XmlConnect_Model_Simplexml_Form_Abstract $fieldset, $layout)
    {
        if (is_object(Mage::getConfig()->getNode('modules/Enterprise_Customer'))) {
            $attrBlock = $layout->addBlock('enterprise_customer/form', $this->getAttributesBlockName());
            $attrBlock->setFormCode($this->getFormCode());
            $attrBlock->setEntity($this->getBlockEntity());
            if ($this->getBlockEntityType()) {
                $attrBlock->setEntityType($this->getBlockEntityType());
            }
            foreach ($this->_customerFiledRenderer as $type => $rendererBlock) {
                $attrBlock->addRenderer($type, $rendererBlock, '');
            }
            if ($attrBlock->hasUserDefinedAttributes()) {
                foreach ($attrBlock->getUserDefinedAttributes() as $attribute) {
                    $type = $attribute->getFrontendInput();
                    $block = $attrBlock->getRenderer($type);
                    if ($block) {
                        $block->setAttributeObject($attribute)->setEntity($attrBlock->getEntity())
                            ->addFieldToXmlObj($fieldset);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Set attributes block name
     *
     * @param string $attributesBlockName
     * @return Mage_XmlConnect_Helper_Form
     */
    public function setAttributesBlockName($attributesBlockName)
    {
        $this->_attributesBlockName = $attributesBlockName;
        return $this;
    }

    /**
     * Get attributes block name
     *
     * @return string
     */
    public function getAttributesBlockName()
    {
        return $this->_attributesBlockName;
    }

    /**
     * Set form code
     *
     * @param string $customerFormCode
     * @return Mage_XmlConnect_Helper_Form
     */
    public function setFormCode($customerFormCode)
    {
        $this->_formCode = $customerFormCode;
        return $this;
    }

    /**
     * Get form code
     *
     * @return string
     */
    public function getFormCode()
    {
        return $this->_formCode;
    }

    /**
     * Set block entity
     *
     * @param object $blockEntity
     * @return Mage_XmlConnect_Helper_Form
     */
    public function setBlockEntity($blockEntity)
    {
        $this->_blockEntity = $blockEntity;
        return $this;
    }

    /**
     * Get block entity
     *
     * @return object
     */
    public function getBlockEntity()
    {
        return $this->_blockEntity;
    }

    /**
     * Get title and required attributes for a field
     *
     * @param Mage_XmlConnect_Model_Simplexml_Form_Abstract $fieldsetXmlObj
     * @param Enterprise_Eav_Block_Form_Renderer_Abstract $blockObject
     * @return array
     */
    public function addTitleAndRequiredAttr(Mage_XmlConnect_Model_Simplexml_Form_Abstract $fieldsetXmlObj,
        Enterprise_Eav_Block_Form_Renderer_Abstract $blockObject
    ) {
        $attributes = array();

        if ($blockObject->isRequired()) {
            $attributes += $fieldsetXmlObj->checkAttribute('required', (int)$blockObject->isRequired());
        }

        if ($blockObject->getAdditionalDescription()) {
            $attributes += $fieldsetXmlObj->checkAttribute('title', $blockObject->getAdditionalDescription());
        }

        return $attributes;
    }

    /**
     * Set block entity type
     *
     * @param string $setBlockEntity
     * @return Mage_XmlConnect_Helper_Customer_Form_Renderer
     */
    public function setBlockEntityType($setBlockEntity)
    {
        $this->_blockEntityType = $setBlockEntity;
        return $this;
    }

    /**
     * Get block entity type
     *
     * @return null|string
     */
    public function getBlockEntityType()
    {
        return $this->_blockEntityType;
    }
}
