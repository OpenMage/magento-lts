<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml grid item abstract renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

abstract class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract extends Mage_Adminhtml_Block_Abstract implements Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Interface
{

    protected $_column;

    public function setColumn($column)
    {
        $this->_column = $column;
        return $this;
    }

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
            return $this->_getValue($row) . ( $this->getColumn()->getEditOnly() ? '' : '</td><td>' ) . $this->_getInputValueElement($row);
        }
        return $this->_getValue($row);
    }

    protected function _getValue(Varien_Object $row)
    {
        if ($getter = $this->getColumn()->getGetter()) {
            return $row->$getter();
        }
        return $row->getData($this->getColumn()->getIndex());
    }

    public function _getInputValueElement(Varien_Object $row)
    {
        return '<input type="text" class="input-text ' . $this->getColumn()->getValidateClass() . '" name="'.( $this->getColumn()->getName() ? $this->getColumn()->getName() : $this->getColumn()->getId() ).'" value="'.$this->_getInputValue($row).'"/>';
    }

    protected function _getInputValue(Varien_Object $row)
    {
        return $this->_getValue($row);
    }

    public function renderHeader()
    {
        $out = '';
        if ( (false !== $this->getColumn()->getGrid()->getSortable()) && (false !== $this->getColumn()->getSortable()) ) {

            $className = 'not-sort';
            $dir = strtolower($this->getColumn()->getDir());
            $nDir= ($dir=='asc') ? 'desc' : 'asc';
            if ($this->getColumn()->getDir()) {
                $className = 'sort-arrow-' . $dir;
            }
            $out = '<a href="#" name="'.$this->getColumn()->getId().'" target="'.$nDir
                   .'" class="' . $className . '"><span class="sort-title">'.$this->getColumn()->getHeader().'</span></a>';
        }
        else {
            $out = $this->getColumn()->getHeader();
        }
        return $out;
    }

    public function renderProperty()
    {
        $out = ' ';
        if ($this->getColumn()->getEditable() && !$this->getColumn()->getEditOnly()) {
            $out .=' span="2"';
        }

        if ($width = $this->getColumn()->getWidth()) {
            if (is_numeric($width)) {
               # $width .= '%';
            } else {
                $width = (int)$width;
            }
            $out .='width="'.$width . '" ';
        }
        return $out;
    }

    public function renderCss()
    {
        return $this->getColumn()->getCssClass();
    }

}
