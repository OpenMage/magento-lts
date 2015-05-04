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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect module observer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Observer
{
    /**
     * List of config field names which changing affects mobile applications behaviour
     *
     * @var array
     */
    protected $_appDependOnConfigFieldPathes = array(
        Mage_XmlConnect_Model_Application::XML_PATH_PAYPAL_BUSINESS_ACCOUNT,
        Mage_Checkout_Helper_Data::XML_PATH_GUEST_CHECKOUT,
        'sendfriend/email/max_recipients',
        'sendfriend/email/allow_guest',
        'general/locale/code',
        'currency/options/default',
        Mage_XmlConnect_Model_Application::XML_PATH_SECURE_BASE_LINK_URL,
        Mage_XmlConnect_Model_Application::XML_PATH_GENERAL_RESTRICTION_IS_ACTIVE,
        Mage_XmlConnect_Model_Application::XML_PATH_GENERAL_RESTRICTION_MODE,
        Mage_XmlConnect_Model_Application::XML_PATH_DEFAULT_CACHE_LIFETIME
    );

    /**
     * Stop website stub or private sales restriction
     *
     * @param Varien_Event_Observer $observer
     */
    public function restrictWebsite($observer)
    {
        $controller = $observer->getEvent()->getController();
        if ($controller instanceof Mage_XmlConnect_Controller_AdminAction
            || $controller instanceof Mage_XmlConnect_Controller_Action
            || Mage::app()->getRequest()->getModuleName() == 'xmlconnect'
        ) {
            $observer->getEvent()->getResult()->setShouldProceed(false);
        }
    }

    /**
     * Update all applications "updated at" parameter with current date on save some configurations
     *
     * @param Varien_Event_Observer $observer
     */
    public function changeUpdatedAtParamOnConfigSave($observer)
    {
        $configData = $observer->getEvent()->getConfigData();
        if ($configData && (int)$configData->isValueChanged()
            && in_array($configData->getPath(), $this->_appDependOnConfigFieldPathes)
        ) {
            Mage::getModel('xmlconnect/application')->updateAllAppsUpdatedAtParameter();
        }
    }

    /**
     * Send a message if Start Date (Queue Date) is empty
     *
     * @param Varien_Event_Observer $observer
     * @return bool
     */
    public function sendMessageImmediately($observer)
    {
        /** @var $message Mage_XmlConnect_Model_Queue */
        $message = $observer->getEvent()->getData('queueMessage');
        $execTime = strtotime($message->getExecTime());
        $isPastTime = false;
        if (false !== $execTime) {
            $isPastTime = strtotime(Mage::getSingleton('core/date')->gmtDate()) - strtotime($message->getExecTime());
        }
        if ($isPastTime === false || $isPastTime > 0) {
            $message->setExecTime(Mage::getSingleton('core/date')->gmtDate());
            Mage::helper('xmlconnect')->sendBroadcastMessage($message);
            return true;
        }
        return false;
    }

    /**
     * Send scheduled messages
     *
     * @return null
     */
    public function scheduledSend()
    {
        $countOfQueue = Mage::getStoreConfig(Mage_XmlConnect_Model_Queue::XML_PATH_CRON_MESSAGES_COUNT);
        $collection = Mage::getModel('xmlconnect/queue')->getCollection()->addOnlyForSendingFilter()
            ->setPageSize($countOfQueue)->setCurPage(1)->load();
        foreach ($collection as $message) {
            if ($message->getId()) {
                Mage::helper('xmlconnect')->sendBroadcastMessage($message);
            }
        }
    }

    /**
     * Clear category images cache
     *
     * @return null
     */
    public function clearCategoryImagesCache()
    {
        Mage::getModel('xmlconnect/catalog_category_image')->clearCache();
    }

    /**
     * Handle xmlconnect admin actions
     *
     * @param Varien_Event_Observer $event
     * @return null
     */
    public function actionFrontPreDispatchXmlAdmin($event)
    {
        /** @var $request Mage_Core_Controller_Request_Http */
        $request = Mage::app()->getRequest();
        if (true === $this->_checkAdminController($request, $event->getControllerAction())) {
            $request->setInternallyForwarded()->setDispatched(true);
        }
    }

    /**
     * Forward unauthorized users for xmlconnect admin actions
     *
     * @param Varien_Event_Observer $event
     * @return null
     */
    public function actionPreDispatchXmlAdmin($event)
    {
        /** @var $request Mage_Core_Controller_Request_Http */
        $request = Mage::app()->getRequest();
        if (false === $this->_checkAdminController($request, $event->getControllerAction())
            && !Mage::getSingleton('admin/session')->isLoggedIn()
        ) {
            $request->setInternallyForwarded()->setRouteName('adminhtml')->setControllerName('connect_user')
                ->setActionName('loginform')->setDispatched(false);
        }
    }

    /**
     * Check is controller action is allowed w/o authorization
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_XmlConnect_Controller_AdminAction $controllerAction
     * @return bool|null
     */
    protected function _checkAdminController($request, $controllerAction)
    {
        if ($controllerAction instanceof Mage_XmlConnect_Controller_AdminAction) {
            foreach ($controllerAction->getAllowedControllerActions() as $controller => $allowedActions) {
                if ($request->getControllerName() == $controller
                    && in_array(strtolower($request->getActionName()), $allowedActions)
                ) {
                    return true;
                }
            }
            return false;
        }
    }
}
