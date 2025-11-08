<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Data form
 *
 * @package    Varien_Data
 *
 * @method string getFieldNameSuffix()
 * @method string getHtmlIdPrefix()
 * @method string getHtmlIdSuffix()
 * @method setDataObject(Mage_Core_Model_Abstract $value)
 * @method $this setFieldNameSuffix(string $value)
 * @method $this setHtmlIdPrefix(string $value)
 */
class Varien_Data_Form extends Varien_Data_Form_Abstract
{
    /**
     * All form elements collection
     *
     * @var Varien_Data_Form_Element_Collection
     */
    protected $_allElements;

    /**
     * form elements index
     *
     * @var array
     */
    protected $_elementsIndex;

    protected static $_defaultElementRenderer;

    protected static $_defaultFieldsetRenderer;

    protected static $_defaultFieldsetElementRenderer;

    /**
     * @inheritDoc
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->_allElements = new Varien_Data_Form_Element_Collection($this);
    }

    public static function setElementRenderer(Varien_Data_Form_Element_Renderer_Interface $renderer)
    {
        self::$_defaultElementRenderer = $renderer;
    }

    public static function setFieldsetRenderer(Varien_Data_Form_Element_Renderer_Interface $renderer)
    {
        self::$_defaultFieldsetRenderer = $renderer;
    }

    public static function setFieldsetElementRenderer(Varien_Data_Form_Element_Renderer_Interface $renderer)
    {
        self::$_defaultFieldsetElementRenderer = $renderer;
    }

    /**
     * @return Varien_Data_Form_Element_Renderer_Interface
     */
    public static function getElementRenderer()
    {
        return self::$_defaultElementRenderer;
    }

    /**
     * @return Varien_Data_Form_Element_Renderer_Interface
     */
    public static function getFieldsetRenderer()
    {
        return self::$_defaultFieldsetRenderer;
    }

    /**
     * @return Varien_Data_Form_Element_Renderer_Interface
     */
    public static function getFieldsetElementRenderer()
    {
        return self::$_defaultFieldsetElementRenderer;
    }

    /**
     * Return allowed HTML form attributes
     * @return array
     */
    public function getHtmlAttributes()
    {
        return ['id', 'name', 'method', 'action', 'enctype', 'class', 'onsubmit'];
    }

    /**
     * Add form element
     *
     * @param false|string $after
     * @return Varien_Data_Form
     * @throws Exception
     */
    public function addElement(Varien_Data_Form_Element_Abstract $element, $after = false)
    {
        $this->checkElementId($element->getId());
        parent::addElement($element, $after);
        $this->addElementToCollection($element);
        return $this;
    }

    /**
     * Check existing element
     *
     * @param   string $elementId
     * @return  bool
     */
    protected function _elementIdExists($elementId)
    {
        return isset($this->_elementsIndex[$elementId]);
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return $this
     */
    public function addElementToCollection($element)
    {
        $this->_elementsIndex[$element->getId()] = $element;
        $this->_allElements->add($element);
        return $this;
    }

    /**
     * @param string $elementId
     * @return bool
     * @throws Exception
     */
    public function checkElementId($elementId)
    {
        if ($this->_elementIdExists($elementId)) {
            throw new Exception('Element with id "' . $elementId . '" already exists');
        }

        return true;
    }

    /**
     * @return $this
     */
    public function getForm()
    {
        return $this;
    }

    /**
     * @param string $elementId
     * @return null|Varien_Data_Form_Element_Abstract
     */
    public function getElement($elementId)
    {
        if ($this->_elementIdExists($elementId)) {
            return $this->_elementsIndex[$elementId];
        }

        return null;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function setValues($values)
    {
        foreach ($this->_allElements as $element) {
            if (isset($values[$element->getId()])) {
                $element->setValue($values[$element->getId()]);
            } else {
                $element->setValue(null);
            }
        }

        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function addValues($values)
    {
        if (!is_array($values)) {
            return $this;
        }

        foreach ($values as $elementId => $value) {
            if ($element = $this->getElement($elementId)) {
                $element->setValue($value);
            }
        }

        return $this;
    }

    /**
     * Add suffix to name of all elements
     *
     * @param string $suffix
     * @return Varien_Data_Form
     */
    public function addFieldNameSuffix($suffix)
    {
        foreach ($this->_allElements as $element) {
            $name = $element->getName();
            if ($name) {
                $element->setName($this->addSuffixToName($name, $suffix));
            }
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $suffix
     * @return string
     */
    public function addSuffixToName($name, $suffix)
    {
        if (!$name) {
            return $suffix;
        }

        $vars = explode('[', $name);
        $newName = $suffix;
        foreach ($vars as $index => $value) {
            $newName .= '[' . $value;
            if ($index == 0) {
                $newName .= ']';
            }
        }

        return $newName;
    }

    /**
     * @param string $elementId
     * @return $this|Varien_Data_Form_Abstract
     */
    public function removeField($elementId)
    {
        if ($this->_elementIdExists($elementId)) {
            unset($this->_elementsIndex[$elementId]);
        }

        return $this;
    }

    /**
     * @param string $prefix
     * @return $this
     */
    public function setFieldContainerIdPrefix($prefix)
    {
        $this->setData('field_container_id_prefix', $prefix);
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldContainerIdPrefix()
    {
        return $this->getData('field_container_id_prefix');
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        Varien_Profiler::start('form/toHtml');
        $html = '';
        if ($useContainer = $this->getUseContainer()) {
            $html .= '<form ' . $this->serialize($this->getHtmlAttributes()) . '>';
            $html .= '<div>';
            if (strtolower((string) $this->getData('method')) == 'post') {
                $html .= '<input name="form_key" type="hidden" value="' . Mage::getSingleton('core/session')->getFormKey() . '" />';
            }

            $html .= '</div>';
        }

        foreach ($this->getElements() as $element) {
            $html .= $element->toHtml();
        }

        if ($useContainer) {
            $html .= '</form>';
        }

        Varien_Profiler::stop('form/toHtml');
        return $html;
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->toHtml();
    }
}
