<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_AdminNotification
 */

/**
 * AdminNotification observer
 *
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
