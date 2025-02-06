<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 */

/**
 * AdminNotification observer
 *
 * @category   Mage
 * @package    Mage_AdminNotification
 */
class Mage_AdminNotification_Model_Observer
{
    /**
     * Predispath admin action controller
     */
    public function preDispatch(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $feedModel  = Mage::getModel('adminnotification/feed');
            /** @var Mage_AdminNotification_Model_Feed $feedModel */

            $feedModel->checkUpdate();
        }
    }
}
