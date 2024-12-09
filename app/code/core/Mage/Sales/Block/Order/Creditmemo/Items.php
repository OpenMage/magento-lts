<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales order view items block
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Order_Creditmemo_Items extends Mage_Sales_Block_Items_Abstract
{
    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return string
     */
    public function getPrintCreditmemoUrl($creditmemo)
    {
        return Mage::getUrl('*/*/printCreditmemo', ['creditmemo_id' => $creditmemo->getId()]);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getPrintAllCreditmemosUrl($order)
    {
        return Mage::getUrl('*/*/printCreditmemo', ['order_id' => $order->getId()]);
    }

    /**
     * Get creditmemo totals block html
     *
     * @param   Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return  string
     */
    public function getTotalsHtml($creditmemo)
    {
        $totals = $this->getChild('creditmemo_totals');
        $html = '';
        if ($totals) {
            $totals->setCreditmemo($creditmemo);
            $html = $totals->toHtml();
        }
        return $html;
    }

    /**
     * Get html of creditmemo comments block
     *
     * @param   Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return  string
     */
    public function getCommentsHtml($creditmemo)
    {
        $html = '';
        $comments = $this->getChild('creditmemo_comments');
        if ($comments) {
            $comments->setEntity($creditmemo)
                ->setTitle(Mage::helper('sales')->__('About Your Refund'));
            $html = $comments->toHtml();
        }
        return $html;
    }
}
