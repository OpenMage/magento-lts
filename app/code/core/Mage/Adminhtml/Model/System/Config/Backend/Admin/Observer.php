<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Admin_Observer
{
    /**
     * Log out user and redirect him to new admin custom url
     *
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings("PHPMD.ExitExpression")
     */
    public function afterCustomUrlChanged($observer)
    {
        if (is_null(Mage::registry('custom_admin_path_redirect'))) {
            return;
        }

        /** @var Mage_Admin_Model_Session $adminSession */
        $adminSession = Mage::getSingleton('admin/session');
        $adminSession->unsetAll();
        $adminSession->getCookie()->delete($adminSession->getSessionName());

        $route = ((bool) (string) Mage::getConfig()->getNode(Mage_Adminhtml_Helper_Data::XML_PATH_USE_CUSTOM_ADMIN_PATH))
            ? Mage::getConfig()->getNode(Mage_Adminhtml_Helper_Data::XML_PATH_CUSTOM_ADMIN_PATH)
            : Mage::getConfig()->getNode(Mage_Adminhtml_Helper_Data::XML_PATH_ADMINHTML_ROUTER_FRONTNAME);

        Mage::app()->getResponse()
            ->setRedirect(Mage::getBaseUrl() . $route)
            ->sendResponse();
        exit(0);
    }
}
