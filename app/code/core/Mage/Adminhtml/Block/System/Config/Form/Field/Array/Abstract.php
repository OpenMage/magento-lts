<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml system config array field renderer
 *
 * @package    Mage_Adminhtml
 */
abstract class Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Grid columns
     *
     * @var array<string, array{label: string, size: string|false, style: ?string, class: ?string, renderer: Mage_Core_Block_Abstract|false}>
     */
    protected $_columns = [];

    /**
     * Enable the "Add after" button or not
     *
     * @var bool
     */
    protected $_addAfter = true;

    /**
     * Label of add button
     *
     * @var string
     */
    protected $_addButtonLabel;

    /**
     * Rows cache
     *
     * @var array<string, Varien_Object>|null
     */
    protected $_arrayRowsCache;

    /**
     * Indication whether block is prepared to render or no
     *
     * @var bool
     */
    protected $_isPreparedToRender = false;

    /**
     * Check if columns are defined, set template
     *
     */
    public function __construct()
    {
        if (!$this->_addButtonLabel) {
            $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add');
        }

        parent::__construct();
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/form/field/array.phtml');
        }
    }

    /**
     * Add a column to array-grid
     *
     * @param string $name
     * @param array $params
     */
    public function addColumn($name, $params)
    {
        $this->_columns[$name] = [
            'label'     => empty($params['label']) ? 'Column' : $params['label'],
            'size'      => empty($params['size']) ? false : $params['size'],
            'style'     => empty($params['style']) ? null : $params['style'],
            'class'     => empty($params['class']) ? null : $params['class'],
            'renderer'  => false,
        ];
        if ((!empty($params['renderer'])) && ($params['renderer'] instanceof Mage_Core_Block_Abstract)) {
            $this->_columns[$name]['renderer'] = $params['renderer'];
        }
    }

    /**
     * Get the grid and scripts contents
     *
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $html = $this->_toHtml();
        $this->_arrayRowsCache = null; // doh, the object is used as singleton!
        return $html;
    }

    /**
     * Prepare existing row data object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        // override in descendants
    }

    /**
     * Obtain existing data from form element
     *
     * Each row will be instance of Varien_Object
     *
     * @return array
     */
    public function getArrayRows()
    {
        if ($this->_arrayRowsCache !== null) {
            return $this->_arrayRowsCache;
        }

        $result = [];
        /** @var Varien_Data_Form_Element_Abstract $element */
        $element = $this->getElement();
        if ($element->getValue() && is_array($element->getValue())) {
            foreach ($element->getValue() as $rowId => $row) {
                foreach ($row as $key => $value) {
                    $row[$key] = $this->escapeHtml($value);
                }

                $row['_id'] = $rowId;
                $result[$rowId] = new Varien_Object($row);
                $this->_prepareArrayRow($result[$rowId]);
            }
        }

        $this->_arrayRowsCache = $result;
        return $this->_arrayRowsCache;
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }

        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if ($column['renderer']) {
            return $column['renderer']->setInputName($inputName)->setColumnName($columnName)->setColumn($column)
                ->toHtml();
        }

        return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            ($column['class'] ?? 'input-text') . '"' .
            (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        // Override in descendants to add columns, change add button label etc
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_isPreparedToRender) {
            $this->_prepareToRender();
            $this->_isPreparedToRender = true;
        }

        if (empty($this->_columns)) {
            throw new Exception('At least one column must be defined.');
        }

        return parent::_toHtml();
    }
}
