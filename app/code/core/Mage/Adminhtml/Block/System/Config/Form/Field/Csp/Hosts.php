<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Csp
 */

/**
 * Base class for CSP hosts field renderer
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Csp_Hosts extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected Mage_Csp_Helper_Data $helper;

    /**
     * Constructor
     */
    public function __construct()
    {
        /** @var Mage_Csp_Helper_Data $helper */
        $helper = Mage::helper('csp');
        $this->helper = $helper;
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
     * @return array<string, Varien_Object> Array of rows
     * @throws Exception
     */
    public function getArrayRows(): array
    {
        if ($this->_arrayRowsCache !== null) {
            return $this->_arrayRowsCache;
        }

        $result = [];

        [$area, $directiveName] = $this->_parseNodePath();

        $globalPolicy = $this->helper->getGlobalPolicy($directiveName);
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

        $areaPolicy = $this->helper->getAreaPolicy($area, $directiveName);
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

        $configPolicy = $this->helper->getStoreConfigPolicy($area, $directiveName);
        foreach ($configPolicy as $key => $value) {
            $rowId = $directiveName . '_' . $area . '_' . $key;
            $result[$rowId] = new Varien_Object([
                'host' => $this->escapeHtml($value),
                '_id' => $rowId,
            ]);

            $this->_prepareArrayRow($result[$rowId]);
        }

        $this->_arrayRowsCache = $result;
        return $this->_arrayRowsCache;
    }

    /**
     * Extract and validate area and directive name from the node path
     *
     * @return array{Mage_Core_Model_App_Area::AREA_FRONTEND|Mage_Core_Model_App_Area::AREA_ADMINHTML, value-of<Mage_Csp_Helper_Data::CSP_DIRECTIVES>} Array containing area and directiveName
     * @throws Exception If path format is invalid or contains disallowed values
     */
    private function _parseNodePath(): array
    {
        /** @var Varien_Data_Form_Element_Abstract $element */
        $element = $this->getElement();
        $configPath = $element->getData('config_path');

        $allowedDirectives = implode('|', Mage_Csp_Helper_Data::CSP_DIRECTIVES);
        $allowedAreas = Mage_Core_Model_App_Area::AREA_FRONTEND . '|' . Mage_Core_Model_App_Area::AREA_ADMINHTML;

        $pattern = "#csp/({$allowedAreas})/({$allowedDirectives})#";

        if (!$configPath || !preg_match($pattern, $configPath, $matches)) {
            throw new Exception('Invalid node path format or disallowed area/directive');
        }

        $area = $matches[1];
        $directiveName = $matches[2];

        return [$area, $directiveName];
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     * @throws Exception
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }

        $column      = $this->_columns[$columnName];
        /** @var Varien_Data_Form_Element_Text $element */
        $element     = $this->getElement();
        $elementName = $element->getName();
        $inputName   = $elementName . '[#{_id}][' . $columnName . ']';

        return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' .
            '#{readonly}' .
            ($column['size'] ? 'size="' . $column['size'] . '"' : '') . ' class="' .
            ($column['class'] ?? 'input-text') . '"' .
            (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';
    }
}
