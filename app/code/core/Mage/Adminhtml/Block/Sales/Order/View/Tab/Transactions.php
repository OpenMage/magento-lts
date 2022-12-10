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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order transactions tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
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
        return true;
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
