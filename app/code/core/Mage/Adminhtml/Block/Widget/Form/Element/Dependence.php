<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Form element dependencies mapper
 * Assumes that one element may depend on other element values.
 * Will toggle as "enabled" only if all elements it depends from toggle as true.
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Form_Element_Dependence extends Mage_Adminhtml_Block_Abstract
{
    /**
     * name => id mapper
     * @var array
     */
    protected $_fields = [];

    /**
     * Dependencies mapper (by names)
     * array(
     *     'dependent_name' => array(
     *         'depends_from_1_name' => 'mixed value',
     *         'depends_from_2_name' => 'some another value',
     *         ...
     *     )
     * )
     * @var array
     */
    protected $_depends = [];

    /**
     * Additional configuration options for the dependencies javascript controller
     *
     * @var array
     */
    protected $_configOptions = [];

    /**
     * Add name => id mapping
     *
     * @param  string $fieldId   - element ID in DOM
     * @param  string $fieldName - element name in their fieldset/form namespace
     * @return $this
     */
    public function addFieldMap($fieldId, $fieldName)
    {
        $this->_fields[$fieldName] = $fieldId;
        return $this;
    }

    /**
     * Register field name dependence one from each other by specified values
     *
     * @param  string       $fieldName
     * @param  string       $fieldNameFrom
     * @param  array|string $refValues
     * @return $this
     */
    public function addFieldDependence($fieldName, $fieldNameFrom, $refValues)
    {
        $this->_depends[$fieldName][$fieldNameFrom] = $refValues;
        return $this;
    }

    /**
     * Add misc configuration options to the javascript dependencies controller
     *
     * @return $this
     */
    public function addConfigOptions(array $options)
    {
        $this->_configOptions = array_merge($this->_configOptions, $options);
        return $this;
    }

    /**
     * HTML output getter
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_depends) {
            return '';
        }

        return '<script type="text/javascript"> new FormElementDependenceController('
            . $this->_getDependsJson()
            . ($this->_configOptions ? ', ' . Mage::helper('core')->jsonEncode($this->_configOptions) : '')
            . '); </script>';
    }

    /**
     * Field dependencies JSON map generator
     * @return string
     */
    protected function _getDependsJson()
    {
        $result = [];
        foreach ($this->_depends as $to => $row) {
            foreach ($row as $from => $value) {
                $result[$this->_fields[$to]][$this->_fields[$from]] = $value;
            }
        }

        return Mage::helper('core')->jsonEncode($result);
    }
}
