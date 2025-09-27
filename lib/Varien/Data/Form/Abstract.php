<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
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
 * @package    Varien_Data
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
     * @param bool $readonly
     * @param bool $useDisabled
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
     * @param string|false $after
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

        if (class_exists($className)) {
            $element = new $className($config);
        } else {
            $className = 'Varien_Data_Form_Element_Note';
            $element = new $className($config);
        }
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
