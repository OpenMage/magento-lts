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
 * @category   Mage
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Export filter block
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method bool hasOperation()
 */
class Mage_ImportExport_Block_Adminhtml_Export_Filter extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Helper object.
     *
     * @var Mage_Core_Helper_Abstract
     */
    protected $_helper;

    /**
     * Set grid parameters.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_helper = Mage::helper('importexport');

        $this->setRowClickCallback(null);
        $this->setId('export_filter_grid');
        $this->setDefaultSort('frontend_label');
        $this->setDefaultDir('ASC');
        $this->setPagerVisibility(false);
        $this->setDefaultLimit(null);
        $this->setUseAjax(true);
    }

    /**
     * Date 'from-to' filter HTML.
     *
     * @deprecated
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    protected function _getDateFromToHtml(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        $dateBlock = new Mage_Core_Block_Html_Date([
            'name'         => $this->getFilterElementName($attribute->getAttributeCode()) . '[]',
            'id'           => $this->getFilterElementId($attribute->getAttributeCode()),
            'class'        => 'input-text',
            'format'       => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'extra_params' => 'style="width:85px !important"',
            'image'        => $this->getSkinUrl('images/grid-cal.gif')
        ]);
        return '<strong>' . Mage::helper('importexport')->__('From') . ':</strong>&nbsp;' . $dateBlock->getHtml()
             . '&nbsp;<strong>' . Mage::helper('importexport')->__('To') . ':</strong>&nbsp;'
             . $dateBlock->setId($dateBlock->getId() . '_to')->getHtml();
    }

    /**
     * Input text filter HTML.
     *
     * @deprecated
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    protected function _getInputHtml(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        return '<input type="text" name="' . $this->getFilterElementName($attribute->getAttributeCode())
             . '" class="input-text" style="width:274px;"/>';
    }

    /**
     * Multiselect field filter HTML.
     *
     * @deprecated
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    protected function _getMultiSelectHtml(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if ($attribute->getFilterOptions()) {
            $options = $attribute->getFilterOptions();
        } else {
            $options = $attribute->getSource()->getAllOptions(false);

            foreach ($options as $key => $optionParams) {
                if ($optionParams['value'] === '') {
                    unset($options[$key]);
                    break;
                }
            }
        }
        if (($size = count($options))) {
            $selectBlock = new Mage_Core_Block_Html_Select([
                'name'         => $this->getFilterElementName($attribute->getAttributeCode()) . '[]',
                'id'           => $this->getFilterElementId($attribute->getAttributeCode()),
                'class'        => 'multiselect',
                'extra_params' => 'multiple="multiple" size="' . ($size > 5 ? 5 : ($size < 2 ? 2 : $size))
                                . '" style="width:280px"'
            ]);
            return $selectBlock->setOptions($options)->getHtml();
        } else {
            return Mage::helper('importexport')->__('Attribute does not has options, so filtering is impossible');
        }
    }

    /**
     * Number 'from-to' field filter HTML.
     *
     * @deprecated
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    protected function _getNumberFromToHtml(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        $name = $this->getFilterElementName($attribute->getAttributeCode());
        return '<strong>' . Mage::helper('importexport')->__('From') . ':</strong>&nbsp;'
             . '<input type="text" name="' . $this->getFilterElementName($attribute->getAttributeCode())
             . '[]" class="input-text" style="width:100px;"/>&nbsp;<strong>' . Mage::helper('importexport')->__('To')
             . ':</strong>&nbsp;<input type="text" name="' . $name
             . '[]" class="input-text" style="width:100px;"/>';
    }

    /**
     * Select field filter HTML.
     *
     * @deprecated
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    protected function _getSelectHtml(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if ($attribute->getFilterOptions()) {
            $options = [];

            foreach ($attribute->getFilterOptions() as $value => $label) {
                $options[] = ['value' => $value, 'label' => $label];
            }
        } else {
            $options = $attribute->getSource()->getAllOptions(false);
        }
        if (($size = count($options))) {
            // add empty vaue option
            $firstOption = reset($options);

            if ($firstOption['value'] === '') {
                $options[key($options)]['label'] = '';
            } else {
                array_unshift($options, ['value' => '', 'label' => '']);
            }
            $selectBlock = new Mage_Core_Block_Html_Select([
                'name'         => $this->getFilterElementName($attribute->getAttributeCode()),
                'id'           => $this->getFilterElementId($attribute->getAttributeCode()),
                'class'        => 'select',
                'extra_params' => 'style="width:280px"'
            ]);
            return $selectBlock->setOptions($options)->getHtml();
        } else {
            return Mage::helper('importexport')->__('Attribute does not has options, so filtering is impossible');
        }
    }

    /**
     * Date 'from-to' filter HTML with values
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param mixed $value
     * @return string
     */
    protected function _getDateFromToHtmlWithValue(Mage_Eav_Model_Entity_Attribute $attribute, $value)
    {
        $dateBlock = new Mage_Core_Block_Html_Date([
            'name'         => $this->getFilterElementName($attribute->getAttributeCode()) . '[]',
            'id'           => $this->getFilterElementId($attribute->getAttributeCode()),
            'class'        => 'input-text input-text-range-date',
            'format'       => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'image'        => $this->getSkinUrl('images/grid-cal.gif')
        ]);
        $fromValue = null;
        $toValue   = null;
        if (is_array($value) && count($value) == 2) {
            $fromValue = $this->_helper->escapeHtml(reset($value));
            $toValue   = $this->_helper->escapeHtml(next($value));
        }

        return '<strong>' . Mage::helper('importexport')->__('From') . ':</strong>&nbsp;'
            . $dateBlock->setValue($fromValue)->getHtml()
            . '&nbsp;<strong>' . Mage::helper('importexport')->__('To') . ':</strong>&nbsp;'
            . $dateBlock->setId($dateBlock->getId() . '_to')->setValue($toValue)->getHtml();
    }

    /**
     * Input text filter HTML with value
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param mixed $value
     * @return string
     */
    protected function _getInputHtmlWithValue(Mage_Eav_Model_Entity_Attribute $attribute, $value)
    {
        $html = '<input type="text" name="' . $this->getFilterElementName($attribute->getAttributeCode())
             . '" class="input-text input-text-export-filter"';
        if ($value) {
            $html .= ' value="' . $this->_helper->escapeHtml($value) . '"';
        }

        return $html . ' />';
    }

    /**
     * Multiselect field filter HTML with selected values
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param mixed $value
     * @return string
     */
    protected function _getMultiSelectHtmlWithValue(Mage_Eav_Model_Entity_Attribute $attribute, $value)
    {
        if ($attribute->getFilterOptions()) {
            $options = $attribute->getFilterOptions();
        } else {
            $options = $attribute->getSource()->getAllOptions(false);

            foreach ($options as $key => $optionParams) {
                if ($optionParams['value'] === '') {
                    unset($options[$key]);
                    break;
                }
            }
        }
        if (($size = count($options))) {
            $selectBlock = new Mage_Core_Block_Html_Select([
                'name'         => $this->getFilterElementName($attribute->getAttributeCode()) . '[]',
                'id'           => $this->getFilterElementId($attribute->getAttributeCode()),
                'class'        => 'multiselect multiselect-export-filter',
                'extra_params' => 'multiple="multiple" size="' . ($size > 5 ? 5 : ($size < 2 ? 2 : $size))
            ]);
            return $selectBlock->setOptions($options)
                ->setValue($value)
                ->getHtml();
        } else {
            return Mage::helper('importexport')->__('Attribute does not has options, so filtering is impossible');
        }
    }

    /**
     * Number 'from-to' field filter HTML with selected value.
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param mixed $value
     * @return string
     */
    protected function _getNumberFromToHtmlWithValue(Mage_Eav_Model_Entity_Attribute $attribute, $value)
    {
        $fromValue = null;
        $toValue = null;
        $name = $this->getFilterElementName($attribute->getAttributeCode());
        if (is_array($value) && count($value) == 2) {
            $fromValue = $this->_helper->escapeHtml(reset($value));
            $toValue   = $this->_helper->escapeHtml(next($value));
        }

        return '<strong>' . Mage::helper('importexport')->__('From') . ':</strong>&nbsp;'
             . '<input type="text" name="' . $name . '[]" class="input-text input-text-range"'
             . ' value="' . $fromValue . '"/>&nbsp;'
             . '<strong>' . Mage::helper('importexport')->__('To')
             . ':</strong>&nbsp;<input type="text" name="' . $name
             . '[]" class="input-text input-text-range" value="' . $toValue . '" />';
    }

    /**
     * Select field filter HTML with selected value.
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param mixed $value
     * @return string
     */
    protected function _getSelectHtmlWithValue(Mage_Eav_Model_Entity_Attribute $attribute, $value)
    {
        if ($attribute->getFilterOptions()) {
            $options = [];

            foreach ($attribute->getFilterOptions() as $value => $label) {
                $options[] = ['value' => $value, 'label' => $label];
            }
        } else {
            $options = $attribute->getSource()->getAllOptions(false);
        }
        if (($size = count($options))) {
            // add empty vaue option
            $firstOption = reset($options);

            if ($firstOption['value'] === '') {
                $options[key($options)]['label'] = '';
            } else {
                array_unshift($options, ['value' => '', 'label' => '']);
            }
            $selectBlock = new Mage_Core_Block_Html_Select([
                'name'         => $this->getFilterElementName($attribute->getAttributeCode()),
                'id'           => $this->getFilterElementId($attribute->getAttributeCode()),
                'class'        => 'select select-export-filter'
            ]);
            return $selectBlock->setOptions($options)
                ->setValue($value)
                ->getHtml();
        } else {
            return Mage::helper('importexport')->__('Attribute does not has options, so filtering is impossible');
        }
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('skip', [
            'header'     => Mage::helper('importexport')->__('Skip'),
            'type'       => 'checkbox',
            'name'       => 'skip',
            'field_name' => Mage_ImportExport_Model_Export::FILTER_ELEMENT_SKIP . '[]',
            'filter'     => false,
            'sortable'   => false,
            'align'      => 'center',
            'index'      => 'attribute_id'
        ]);
        $this->addColumn('frontend_label', [
            'header'   => Mage::helper('importexport')->__('Attribute Label'),
            'index'    => 'frontend_label'
        ]);
        $this->addColumn('attribute_code', [
            'header' => Mage::helper('importexport')->__('Attribute Code'),
            'index'  => 'attribute_code'
        ]);
        $this->addColumn('filter', [
            'header'         => Mage::helper('importexport')->__('Filter'),
            'sortable'       => false,
            'filter'         => false,
            'frame_callback' => [$this, 'decorateFilter']
        ]);

        if ($this->hasOperation()) {
            $operation = $this->getOperation();
            $skipAttr = $operation->getSkipAttr();
            if ($skipAttr) {
                $this->getColumn('skip')
                    ->setData('values', $skipAttr);
            }
            $filter = $operation->getExportFilter();
            if ($filter) {
                $this->getColumn('filter')
                    ->setData('values', $filter);
            }
        }

        return $this;
    }

    /**
     * Create filter fields for 'Filter' column.
     *
     * @param mixed $value
     * @param Mage_Eav_Model_Entity_Attribute $row
     * @param Varien_Object $column
     * @param bool $isExport
     * @return string
     */
    public function decorateFilter($value, Mage_Eav_Model_Entity_Attribute $row, Varien_Object $column, $isExport)
    {
        $value  = null;
        $values = $column->getValues();
        if (is_array($values) && isset($values[$row->getAttributeCode()])) {
            $value = $values[$row->getAttributeCode()];
        }
        switch (Mage_ImportExport_Model_Export::getAttributeFilterType($row)) {
            case Mage_ImportExport_Model_Export::FILTER_TYPE_SELECT:
                $cell = $this->_getSelectHtmlWithValue($row, $value);
                break;
            case Mage_ImportExport_Model_Export::FILTER_TYPE_INPUT:
                $cell = $this->_getInputHtmlWithValue($row, $value);
                break;
            case Mage_ImportExport_Model_Export::FILTER_TYPE_DATE:
                $cell = $this->_getDateFromToHtmlWithValue($row, $value);
                break;
            case Mage_ImportExport_Model_Export::FILTER_TYPE_NUMBER:
                $cell = $this->_getNumberFromToHtmlWithValue($row, $value);
                break;
            default:
                $cell = Mage::helper('importexport')->__('Unknown attribute filter type');
        }
        return $cell;
    }

    /**
     * Element filter ID getter.
     *
     * @param string $attributeCode
     * @return string
     */
    public function getFilterElementId($attributeCode)
    {
        return Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP . "_{$attributeCode}";
    }

    /**
     * Element filter full name getter.
     *
     * @param string $attributeCode
     * @return string
     */
    public function getFilterElementName($attributeCode)
    {
        return Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP . "[{$attributeCode}]";
    }

    /**
     * Get row edit URL.
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $row
     * @return false
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * Prepare collection by setting page number, sorting etc..
     *
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    public function prepareCollection(Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection)
    {
        $this->_collection = $collection;

        $this->_prepareGrid();

        return $this->_collection;
    }
}
