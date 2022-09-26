<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml grid item abstract renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

abstract class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
    extends Mage_Adminhtml_Block_Abstract implements Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Interface
{
    protected $_defaultWidth;
    protected $_column;

    /**
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    public function setColumn($column)
    {
        $this->_column = $column;
        return $this;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid_Column
     */
    public function getColumn()
    {
        return $this->_column;
    }

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ($this->getColumn()->getEditable()) {
            $value = $this->_getValue($row);
            return $value
                   . ($this->getColumn()->getEditOnly() ? '' : ($value != '' ? '' : '&nbsp;'))
                   . $this->_getInputValueElement($row);
        }
        return $this->_getValue($row);
    }

    /**
     * Render column for export
     *
     * @param Varien_Object $row
     * @return string
     */
    public function renderExport(Varien_Object $row)
    {
        return $this->render($row);
    }

    /**
     * @param Varien_Object $row
     * @return string|null
     */
    protected function _getValue(Varien_Object $row)
    {
        if ($getter = $this->getColumn()->getGetter()) {
            if (is_string($getter)) {
                return $row->$getter();
            } elseif (is_callable($getter)) {
                return call_user_func($getter, $row);
            }
            return '';
        }
        return $row->getData($this->getColumn()->getIndex());
    }

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function _getInputValueElement(Varien_Object $row)
    {
        return  '<input type="text" class="input-text '
                . $this->getColumn()->getValidateClass()
                . '" name="' . $this->getColumn()->getId()
                . '" value="' . $this->_getInputValue($row) . '"/>';
    }

    /**
     * @param Varien_Object $row
     * @return string|null
     */
    protected function _getInputValue(Varien_Object $row)
    {
        return $this->_getValue($row);
    }

    /**
     * @return string
     */
    public function renderHeader()
    {
        if ($this->getColumn()->getGrid()->getSortable() !== false && $this->getColumn()->getSortable() !== false) {
            $className = 'not-sort';
            $dir = strtolower($this->getColumn()->getDir());
            $nDir= ($dir=='asc') ? 'desc' : 'asc';
            if ($this->getColumn()->getDir()) {
                $className = 'sort-arrow-' . $dir;
            }
            $out = '<a href="#" name="' . $this->getColumn()->getId() . '" title="' . $nDir
                   . '" class="' . $className . '"><span class="sort-title">'
                   . $this->escapeHtml($this->getColumn()->getHeader()) . '</span></a>';
        } else {
            $out = $this->escapeHtml($this->getColumn()->getHeader());
        }
        return $out;
    }

    /**
     * @return string
     */
    public function renderProperty()
    {
        $out = '';
        $width = $this->_defaultWidth;

        if ($this->getColumn()->hasData('width')) {
            $customWidth = $this->getColumn()->getData('width');
            if (($customWidth === null) || (preg_match('/^[0-9]+%?$/', $customWidth))) {
                $width = $customWidth;
            }
            elseif (preg_match('/^([0-9]+)px$/', $customWidth, $matches)) {
                $width = (int)$matches[1];
            }
        }

        if ($width !== null) {
            $out .= ' width="' . $width . '"';
        }

        return $out;
    }

    /**
     * @return string|null
     */
    public function renderCss()
    {
        return $this->getColumn()->getCssClass();
    }
}
