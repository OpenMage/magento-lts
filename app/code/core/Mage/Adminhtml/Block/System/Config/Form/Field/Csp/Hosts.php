<?php

declare(strict_types=1);

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
abstract class Mage_Adminhtml_Block_System_Config_Form_Field_Csp_Hosts extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Directive name (e.g., 'script-src', 'style-src')
     *
     * @var string
     */
    protected $_directiveName = '';
    /**
     * Area name adminhtml or frontend
     */
    protected $_area = Mage_Core_Model_App_Area::AREA_FRONTEND;

    /**
    * @var Mage_Csp_Helper_Data
    */
    protected $_helper;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('csp');
        $this->addColumn('host', [
            'label' => Mage::helper('csp')->__('Host'),
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('csp')->__('Add Host');
        $this->setTemplate('system/config/form/field/csp.phtml');

        parent::__construct();
    }

    /**
     * Obtain existing data from form element
     *
     * Each row will be instance of Varien_Object
     */
    public function getArrayRows(): array
    {
        if ($this->_arrayRowsCache !== null) {
            return $this->_arrayRowsCache;
        }

        $result = [];

        $directiveName = $this->_directiveName;
        $area = $this->_area;

        $globalPolicy = $this->_helper->getGlobalPolicy($directiveName);
        if ($globalPolicy) {
            foreach ($globalPolicy as $key => $host) {
                $rowId = $directiveName . '_xml_' . $area . '_' . $key;
                $result[$rowId] = new Varien_Object([
                    'host' => $host,
                    'readonly' => 'readonly="readonly"',
                    '_id' => $rowId,
                    'area' => 'global',
                ]);
                $this->_prepareArrayRow($result[$rowId]);
            }
        }

        $areaPolicy = $this->_helper->getAreaPolicy($area, $directiveName);
        if ($areaPolicy) {
            foreach ($areaPolicy as $key => $host) {
                $rowId = $directiveName . '_xml_' . $area . '_' . $key;
                $result[$rowId] = new Varien_Object([
                    'host' => $host,
                    'readonly' => 'readonly="readonly"',
                    '_id' => $rowId,
                    'area' => $area,
                ]);
                $this->_prepareArrayRow($result[$rowId]);
            }
        }

        $configPolicy = $this->_helper->getStoreConfigPolicy($area, $directiveName);
        if ($configPolicy) {
            foreach ($configPolicy as $key => $value) {
                $rowId = $directiveName . '_' . $area . '_' . $key;
                $result[$rowId] = new Varien_Object([
                    'host' => $this->escapeHtml($value),
                    '_id' => $rowId,
                ]);

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
            '#{readonly}' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            ($column['class'] ?? 'input-text') . '"' .
            (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';
    }
}
