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
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Entity/Attribute/Model - attribute frontend abstract
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Eav_Model_Entity_Attribute_Frontend_Abstract implements Mage_Eav_Model_Entity_Attribute_Frontend_Interface
{

    /**
     * Reference to the attribute instance
     *
     * @var Mage_Eav_Model_Entity_Attribute_Abstract
     */
    protected $_attribute;

    /**
     * Set attribute instance
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
     */
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * Get attribute instance
     *
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    /**
     * Get attribute type for user interface form
     *
     * @return string
     */
    public function getInputType()
    {
        return $this->getAttribute()->getFrontendInput();
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getLabel()
    {
        $label = $this->getAttribute()->getFrontendLabel();
        if (is_null($label) || $label=='') {
            $label = $this->getAttribute()->getAttributeCode();
        }
        return $label;
    }

    public function getValue(Varien_Object $object)
    {
        $value = $object->getData($this->getAttribute()->getAttributeCode());
        if (in_array($this->getConfigField('input'), array('select','boolean'))) {
            $valueOption = $this->getOption($value);
            if (!$valueOption) {
                $opt = new Mage_Eav_Model_Entity_Attribute_Source_Boolean();
                if ($options = $opt->getAllOptions()) {
                    foreach ($options as $option) {
                        if ($option['value'] == $value) {
                            $valueOption = $option['label'];
                        }
                    }
                }
            }
            $value = $valueOption;
        }
        elseif ($this->getConfigField('input')=='multiselect') {
            $value = $this->getOption($value);
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
        }
        return $value;
    }

    public function isVisible()
    {
        return $this->getConfigField('frontend_visible');
    }

    public function getClass()
    {
        $out = $this->getAttribute()->getFrontendClass();
        if ($this->getAttribute()->getIsRequired()) {
            $out .= ' required-entry';
        }
        return $out;
    }

    public function getConfigField($fieldName)
    {
        return $this->getAttribute()->getData('frontend_'.$fieldName);
    }

    /**
     * Get select options in case it's select box and options source is defined
     *
     * @return array
     */
    public function getSelectOptions()
    {
        return $this->getAttribute()->getSource()->getAllOptions();
    }

    public function getOption($optionId)
    {
        if ($source = $this->getAttribute()->getSource()) {
            return $source->getOptionText($optionId);
        }
        return false;
    }

    /**
     * Retrieve Input Renderer Class
     *
     * @return string
     */
    public function getInputRendererClass() {
        if ($className = $this->getAttribute()->getData('frontend_input_renderer')) {
            return Mage::getConfig()->getBlockClassName($className);
        }
        return null;
    }

}
