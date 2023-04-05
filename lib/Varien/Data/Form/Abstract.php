<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract class for form, coumn and fieldset
 *
 * @method Varien_Data_Form getForm()
 * @method bool getUseContainer()
 * @method $this setAction(string $value)
 * @method $this setMethod(string $value)
 * @method $this setName(string $value)
 * @method $this setValue(mixed $value)
 * @method $this setUseContainer(bool $value)
 * @method $this setDisabled(bool $value)
 * @method $this setRequired(bool $value)
 *
 * @category   Varien
 * @package    Varien_Data
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Abstract extends Varien_Object
{
    /**
     * Form level elements collection
     *
     * @var Varien_Data_Form_Element_Collection
     */
    protected $_elements;

    /**
     * Element type classes
     *
     * @var array
     */
    protected $_types = [];

    /**
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @param string $type
     * @param string $className
     * @return $this
     */
    public function addType($type, $className)
    {
        $this->_types[$type] = $className;
        return $this;
    }

    /**
     * @return Varien_Data_Form_Element_Collection
     */
    public function getElements()
    {
        if (empty($this->_elements)) {
            $this->_elements = new Varien_Data_Form_Element_Collection($this);
        }
        return $this->_elements;
    }

    /**
     * Disable elements
     *
     * @param boolean $readonly
     * @param boolean $useDisabled
     * @return $this
     */
    public function setReadonly($readonly, $useDisabled = false)
    {
        if ($useDisabled) {
            $this->setDisabled($readonly);
            $this->setData('readonly_disabled', $readonly);
        } else {
            $this->setData('readonly', $readonly);
        }
        foreach ($this->getElements() as $element) {
            $element->setReadonly($readonly, $useDisabled);
        }

        return $this;
    }

    /**
     * Add form element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @param bool|string|null $after
     *
     * @return $this
     */
    public function addElement(Varien_Data_Form_Element_Abstract $element, $after = null)
    {
        $element->setForm($this);
        $this->getElements()->add($element, $after);
        return $this;
    }

    /**
     * Add child element
     *
     * if $after parameter is false - then element adds to end of collection
     * if $after parameter is null - then element adds to befin of collection
     * if $after parameter is string - then element adds after of the element with some id
     *
     * @param   string $elementId
     * @param   string $type
     * @param   array  $config
     * @param   mixed  $after
     * @return Varien_Data_Form_Element_Abstract
     */
    public function addField($elementId, $type, $config, $after = false)
    {
        if (isset($this->_types[$type])) {
            $className = $this->_types[$type];
        } else {
            $className = 'Varien_Data_Form_Element_' . ucfirst(strtolower($type));
        }
        $element = new $className($config);
        $element->setId($elementId);
        $this->addElement($element, $after);
        return $element;
    }

    /**
     * @param string $elementId
     * @return $this
     */
    public function removeField($elementId)
    {
        $this->getElements()->remove($elementId);
        return $this;
    }

    /**
     * @param string $elementId
     * @param array $config
     * @param bool|string|null $after
     *
     * @return Varien_Data_Form_Element_Fieldset
     */
    public function addFieldset($elementId, $config, $after = false)
    {
        $element = new Varien_Data_Form_Element_Fieldset($config);
        $element->setId($elementId);
        $this->addElement($element, $after);
        return $element;
    }

    /**
     * @param string $elementId
     * @param array $config
     * @return Varien_Data_Form_Element_Column
     */
    public function addColumn($elementId, $config)
    {
        $element = new Varien_Data_Form_Element_Column($config);
        $element->setForm($this)
            ->setId($elementId);
        $this->addElement($element);
        return $element;
    }

    /**
     * @param array $arrAttributes
     * @return array
     */
    public function __toArray(array $arrAttributes = [])
    {
        $res = [];
        $res['config']  = $this->getData();
        $res['formElements'] = [];
        foreach ($this->getElements() as $element) {
            $res['formElements'][] = $element->toArray();
        }
        return $res;
    }
}
