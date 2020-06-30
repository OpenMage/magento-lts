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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Installation event observer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Observer
{

    public function bindLocale($observer)
    {
        if ($locale=$observer->getEvent()->getLocale()) {
            if ($choosedLocale = Mage::getSingleton('adminhtml/session')->getLocale()) {
                $locale->setLocaleCode($choosedLocale);
            }
        }
        return $this;
    }

    public function bindStore()
    {
        Mage::app()->setCurrentStore('admin');
        return $this;
    }

    /**
     * Prepare massaction separated data
     *
     * @return $this
     */
    public function massactionPrepareKey()
    {
        $request = Mage::app()->getFrontController()->getRequest();
        if ($key = $request->getPost('massaction_prepare_key')) {
            $value = is_array($request->getPost($key)) ? $request->getPost($key) : explode(',', $request->getPost($key));
            $request->setPost($key, $value ? $value : null);
        }
        return $this;
    }

    /**
     * Clear result of configuration files access level verification in system cache
     *
     * @return $this
     */
    public function clearCacheConfigurationFilesAccessLevelVerification()
    {
        Mage::app()->removeCache(Mage_Adminhtml_Block_Notification_Security::VERIFICATION_RESULT_CACHE_KEY);
        return $this;
    }

    /**
     * Prevent login from controllers that is not Mage_Adminhtml_IndexController
     *
     * @param Varien_Event_Observer $observer
     */
    public function preventBadControllerLogin(Varien_Event_Observer $observer)
    {

        $event = $observer->getEvent();
        $controller = $event->getData('controller_action');
        $adminConfig = Mage::getConfig()->getNode('admin/routers/adminhtml/args')->asArray();
        $isLoggedIn = Mage::getSingleton('admin/session')->isLoggedIn();
        $adminLoginUrl = Mage::helper('adminhtml')->getUrl('adminhtml');
        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $adminPath = null;

        if ($controller instanceof Mage_Adminhtml_IndexController) {
            return;
        }

        if (isset($adminConfig['frontName']) && is_string($adminConfig['frontName'])) {
            $adminPath = $adminConfig['frontName'];
        }

        if ($controller instanceof Mage_Adminhtml_Controller_Action && $isLoggedIn !== true) {
            $controller->getResponse()->clearHeaders();
            if ($adminPath !== null && stristr($currentUrl, $adminPath) !== false) {
                $controller->getResponse()->setRedirect($adminLoginUrl);
            } else {
                $controller->getResponse()->setBody('');
                $controller->getResponse()->setHttpResponseCode(403);
            }
            $controller->getResponse()->sendResponse();
            exit;
        }
    }
}
