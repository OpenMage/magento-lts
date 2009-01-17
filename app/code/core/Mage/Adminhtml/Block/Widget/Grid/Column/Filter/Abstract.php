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
 * Grid colum filter block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract extends Mage_Adminhtml_Block_Abstract implements Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Interface
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

    protected function _getHtmlName()
    {
        return $this->getColumn()->getId();
    }

    protected function _getHtmlId()
    {
        return $this->getColumn()->getGrid()->getVarNameFilter().'_'.$this->getColumn()->getId();
    }

    public function getEscapedValue($index=null)
    {
        return htmlspecialchars($this->getValue($index));
    }

    public function getCondition()
    {
        return array('like'=>'%'.$this->_escapeValue($this->getValue()).'%');
    }

    protected function _escapeValue($value)
    {
        return str_replace('_', '\_', $value);
    }

    public function getHtml()
    {
        return '';
    }

}

