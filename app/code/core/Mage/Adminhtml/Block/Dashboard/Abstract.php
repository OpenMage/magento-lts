<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml dashboard tab abstract
 *
 * @category   Mage
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
