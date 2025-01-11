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
 * Order transactions tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_View_Tab_Transactions extends Mage_Adminhtml_Block_Sales_Transactions_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Retrieve grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/sales_order/transactions', ['_current' => true]);
    }

    /**
     * Retrieve grid row url
     *
     * @return string
     */
    public function getRowUrl($item)
    {
        return $this->getUrl('*/sales_transactions/view', ['_current' => true, 'txn_id' => $item->getId()]);
    }

    /**
     * Retrieve tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Transactions');
    }

    /**
     * Retrieve tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Transactions');
    }

    /**
     * Check whether can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/transactions');
    }

    /**
     * Check whether tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return !Mage::getSingleton('admin/session')->isAllowed('sales/transactions/fetch');
    }
}
