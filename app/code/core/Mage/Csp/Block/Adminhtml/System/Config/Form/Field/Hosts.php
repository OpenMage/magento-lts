<?php
/**
 * OpenMage
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available at https://opensource.org/license/afl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Csp
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

/**
 * Base class for CSP hosts field renderer
 */
class Mage_Csp_Block_Adminhtml_System_Config_Form_Field_Hosts extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Directive name (e.g., 'script-src', 'style-src')
     *
     * @var string
     */
    protected $_directiveName = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->addColumn('host', [
            'label' => Mage::helper('csp')->__('Host'),
            'style' => 'width:300px',
            'renderer' => 'csp/adminhtml_system_config_form_field_hosts_renderer',
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('csp')->__('Add Host');
        $this->setTemplate('csp/system/config/form/field/array.phtml');

        parent::__construct();
    }

    /**
     * Get directive name
     *
     * @return string
     */
    public function getDirectiveName()
    {
        return $this->_directiveName;
    }

    /**
     * Set directive name
     *
     * @param string $name
     * @return $this
     */
    public function setDirectiveName($name)
    {
        $this->_directiveName = $name;
        return $this;
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

        // Get values from XML files
        $directiveName = $this->getDirectiveName();
        $configNode = Mage::getConfig()->getNode("global/csp/$directiveName");
        if ($configNode) {
            $hosts = $configNode->asArray();
            if ($hosts) {
                foreach ($hosts as $key => $host) {
                    $rowId = $directiveName . '_xml_' . $key;
                    $result[$rowId] = new Varien_Object([
                        'host' => $host,
                        'readonly' => 'readonly="readonly"',
                        '_id' => $rowId,
                    ]);
                    $this->_prepareArrayRow($result[$rowId]);
                }
            }
        }

        // Get values from default config
        $defaultNode = Mage::getConfig()->getNode("default/system/csp/$directiveName");
        if ($defaultNode) {
            $hosts = $defaultNode->asArray();
            if ($hosts) {
                foreach ($hosts as $key => $value) {
                    $rowId = $directiveName . '_default_' . $key;
                    $result[$rowId] = new Varien_Object([
                        'host' => $this->escapeHtml($value),
                        '_id' => $rowId,
                    ]);

                    $this->_prepareArrayRow($result[$rowId]);
                }
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
            '#{readonly}' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            ($column['class'] ?? 'input-text') . '"' .
            (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';
    }
}
