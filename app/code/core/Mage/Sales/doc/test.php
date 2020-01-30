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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
include "lib/Varien/Object.php";

class Test {
    protected $_order;

    public function runTest()
    {
        $this->_order = new Varien_Object;
        #echo "<table border=1><tr><td>Order</td><td>Payment</td><td>Shipping</td><td>Refund</td><td>Return</td><td>Admin Status</td><td>Frontend Status</td><td>Actions</td></tr>";
        echo "<table border=1><tr><td>Order</td><td>Payment</td><td>Refund</td><td>Shipping</td><td>Actions</td></tr>";
        foreach (array('new', 'onhold', 'processing', 'complete', 'closed', 'cancelled', 'void') as $orderStatus) {
            $this->getOrder()->setOrderStatus($orderStatus);
            foreach (array('not_authorized', 'pending', 'authorized', 'partial', 'paid') as $paymentStatus) {
                $this->getOrder()->setPaymentStatus($paymentStatus);
                foreach (array('pending', 'partial', 'shipped') as $shippingStatus) {
                    $this->getOrder()->setShippingStatus($shippingStatus);
                    foreach (array('not_refunded', 'partial', 'refunded') as $refundStatus) {
                        $this->getOrder()->setRefundStatus($refundStatus);
//                        foreach (array('not_returned', 'partial', 'returned') as $returnStatus) {
//                            $this->getOrder()->setReturnStatus($returnStatus);
                            if (!$this->validateOrderStatus()) {
                                continue;
                            }
                            $adminStatus = $this->getAdminStatus();
                            $frontendStatus = $this->getFrontendStatus();
                            $actions = $this->getOrderActions();
                            $actions = join(', ', array_keys($actions));
                            #echo "<tr><td>$orderStatus</td><td>$paymentStatus</td><td>$shippingStatus</td><td>$refundStatus</td><td>$returnStatus</td><td>$adminStatus</td><td>$frontendStatus</td><td>$actions</td></tr>";
                            echo "<tr><td>$orderStatus</td><td>$paymentStatus</td><td>$refundStatus</td><td>$shippingStatus</td><td>$actions</td></tr>";
//                        }
                    }
                }
            }
        }
        echo "</table>";
    }

    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Check if type and status matches for the order
     *
     * @param string $type order, payment, shipment
     * @param string $status comma separated
     * - order
     *   - new
     *   - onhold
     *   - processing
     *   - complete
     *   - closed
     *   - cancelled
     *   - void
     *
     * - payment
     *   - not_authorized
     *   - pending
     *   - authorized
     *   - partial
     *   - paid
     *
     * - shipping
     *   - pending
     *   - partial
     *   - shipped
     *
     * - refund
     *   - not_refunded
     *   - pending
     *   - partial
     *   - refunded
     *
     * - return
     *   - not_returned
     *   - partial
     *   - returned

     */
    function matchOrderStatus($type, $status) {
        $statuses = explode(',', $status);
        $value = $this->getOrder()->getData($type.'_status');
        foreach ($statuses as $status) {
            if ($value==$status) {
                return true;
            }
        }
        return false;
    }

    function validateOrderStatus()
    {
        if ($this->matchOrderStatus('order', 'new')) {
            if (!$this->matchOrderStatus('shipping', 'pending')
//            || !$this->matchOrderStatus('return', 'not_returned')
            || !$this->matchOrderStatus('refund', 'not_refunded')
            ) {
                return false;
            }
            if ($this->matchOrderStatus('payment', 'partial')) {
                return false;
            }
        }

        if ($this->matchOrderStatus('order', 'onhold')) {
            if (!$this->matchOrderStatus('shipping', 'pending')
            || !$this->matchOrderStatus('payment', 'pending')
            || !$this->matchOrderStatus('refund', 'not_refunded')
//            || !$this->matchOrderStatus('return', 'not_returned')
            ) {
                return false;
            }
        }

        if ($this->matchOrderStatus('order', 'cancelled')) {
            if (!$this->matchOrderStatus('shipping', 'pending')
            || !$this->matchOrderStatus('payment', 'pending,not_authorized')
            || !$this->matchOrderStatus('refund', 'not_refunded')
//            || !$this->matchOrderStatus('return', 'not_returned')
            ) {
                return false;
            }
        }

        if ($this->matchOrderStatus('order', 'complete,closed')) {
            if (!$this->matchOrderStatus('payment', 'paid')
            || !$this->matchOrderStatus('shipping', 'shipped')
            ) {
                return false;
            }
        }

        if ($this->matchOrderStatus('order', 'void')) {
            if ($this->matchOrderStatus('payment', 'pending,not_authorized')) {
                return false;
            }
            if (!$this->matchOrderStatus('refund', 'not_refunded')) {
                return false;
            }
        }

        if ($this->matchOrderStatus('payment', 'pending,not_authorized')
        && !$this->matchOrderStatus('refund', 'not_refunded')
        ) {
            return false;
        }

        if ($this->matchOrderStatus('payment', 'authorized')
        && !$this->matchOrderStatus('refund', 'not_refunded')
        ) {
            return false;
        }

        if ($this->matchOrderStatus('payment', 'partial')
        && $this->matchOrderStatus('refund', 'refunded')
        ) {
            return false;
        }

//        if ($this->matchOrderStatus('shipping', 'pending')
//        && !$this->matchOrderStatus('return', 'not_returned')
//        ) {
//            return false;
//        }
//
//        if ($this->matchOrderStatus('shipping', 'partial') && $this->matchOrderStatus('return', 'returned')) {
//            return false;
//        }

        return true;
    }

    /**
     * Available actions for admin user
     *
     * @return array available actions array
     * - cancel
     * - authorize
     * - capture
     * - invoice
     * - creditmemo
     * - hold
     * - unhold
     * - ship
     * - edit
     * - comment
     * - status
     * - reorder
     */
    function getOrderActions()
    {
        $actions = array();

        $actions['comment'] = 1;

        if ($this->matchOrderStatus('order', 'cancelled')) {
            $actions['reorder'] = 1;
            return $actions;
        }

        if ($this->matchOrderStatus('order', 'closed')) {
            $actions['reorder'] = 1;
            if (!$this->matchOrderStatus('refund', 'refunded')) {
                $actions['creditmemo'] = 1;
            }
            return $actions;
        }

        if ($this->matchOrderStatus('order', 'onhold')) {
            $actions['unhold'] = 1;
            return $actions;
        }

        $actions['edit'] = 1;

        $actions['hold'] = 1;

        if (!$this->matchOrderStatus('order', 'void')) {
            $actions['cancel'] = 1;
        }

        if ($this->matchOrderStatus('payment', 'not_authorized')) {
            $actions['authorize'] = 1;
            $actions['capture'] = 1;
        }

        if (!$this->matchOrderStatus('payment', 'not_authorized,pending,paid')) {
            $actions['invoice'] = 1;
        }


        if (!$this->matchOrderStatus('shipping', 'shipped')) {
            $actions['ship'] = 1;
        }

        if ($this->matchOrderStatus('payment', 'partial,paid') && !$this->matchOrderStatus('refund', 'refunded')) {
            $actions['creditmemo'] = 1;
        }

        if ($this->matchOrderStatus('order', 'void')) {
            unset($actions['ship'], $actions['invoice'], $actions['ship'], $actions['hold']);
        }

        return $actions;
    }

    /**
     * Order status for admin
     *
     * @return array
     * - new
     * - pending
     * - processing
     * - complete
     * - cancelled
     */
    function getAdminStatus()
    {
        return $this->getOrder()->getOrderStatus();
    }

    /**
     * Order status for customers
     *
     * @return array
     * - new
     * - pending
     * - processing
     * - complete
     * - cancelled
     */
    function getFrontendStatus()
    {
        return $this->getOrder()->getOrderStatus();
    }
}

$test = new Test;
$test->runTest();
