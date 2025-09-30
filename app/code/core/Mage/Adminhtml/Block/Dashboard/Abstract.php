<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml dashboard tab abstract
 *
 * @package    Mage_Adminhtml
 */
abstract class Mage_Adminhtml_Block_Dashboard_Abstract extends Mage_Adminhtml_Block_Widget
{
    protected $_dataHelperName = null;

    public function getCollection()
    {
        return $this->getDataHelper()->getCollection();
    }

    public function getCount()
    {
        return $this->getDataHelper()->getCount();
    }

    public function getDataHelper()
    {
        return $this->helper($this->getDataHelperName());
    }

    public function getDataHelperName()
    {
        return $this->_dataHelperName;
    }

    public function setDataHelperName($dataHelperName)
    {
        $this->_dataHelperName = $dataHelperName;
        return $this;
    }

    protected function _prepareData()
    {
        return $this;
    }

    protected function _prepareLayout()
    {
        $this->_prepareData();
        return parent::_prepareLayout();
    }
}
