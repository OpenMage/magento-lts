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
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml abandoned shopping cart report page content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Report_Shopcart_Abandoned extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'report_shopcart_abandoned';
        $this->_headerText = Mage::helper('reports')->__('Abandoned carts');
        parent::__construct();
        $this->_removeButton('add');
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'store_switcher',
            $this->getLayout()->createBlock('adminhtml/store_switcher')
                ->setUseConfirm(false)
                ->setSwitchUrl($this->getUrl('*/*/*', ['store' => null]))
                ->setTemplate('report/store/switcher.phtml'),
        );
        return parent::_prepareLayout();
    }

    public function getStoreSwitcherHtml()
    {
        return Mage::app()->isSingleStoreMode() ? '' : $this->getChildHtml('store_switcher');
    }

    public function getGridHtml()
    {
        return $this->getStoreSwitcherHtml() . parent::getGridHtml();
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-report';
    }
}
