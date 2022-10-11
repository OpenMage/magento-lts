<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin observer model
 *
 * @category   Mage
 * @package    Mage_Admin
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Admin_Model_Observer
{
    const FLAG_NO_LOGIN = 'no-login';

    /**
     * Handler for controller_action_predispatch event
     *
     * @param Varien_Event_Observer $observer
     */
    public function actionPreDispatchAdmin($observer)
    {
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');

        $request = Mage::app()->getRequest();
        $user = $session->getUser();

        $requestedActionName = strtolower($request->getActionName());
        $openActions = [
            'forgotpassword',
            'resetpassword',
            'resetpasswordpost',
            'logout',
            'refresh' // captcha refresh
        ];
        if (in_array($requestedActionName, $openActions)) {
            $request->setDispatched(true);
        } else {
            if ($user) {
                $user->reload();
            }
            if (!$user || !$user->getId()) {
                if ($request->getPost('login')) {

                    /** @var Mage_Core_Model_Session $coreSession */
                    $coreSession = Mage::getSingleton('core/session');

                    if ($coreSession->validateFormKey($request->getPost("form_key"))) {
                        $postLogin = $request->getPost('login');
                        $username = isset($postLogin['username']) ? $postLogin['username'] : '';
                        $password = isset($postLogin['password']) ? $postLogin['password'] : '';
                        $session->login($username, $password, $request);
                        $request->setPost('login', null);
                    } else {
                        if ($request && !$request->getParam('messageSent')) {
                            Mage::getSingleton('adminhtml/session')->addError(
                                Mage::helper('adminhtml')->__('Invalid Form Key. Please refresh the page.')
                            );
                            $request->setParam('messageSent', true);
                        }
                    }

                    $coreSession->renewFormKey();
                }
                if (!$request->getInternallyForwarded()) {
                    $request->setInternallyForwarded();
                    if ($request->getParam('isIframe')) {
                        $request->setParam('forwarded', true)
                            ->setControllerName('index')
                            ->setActionName('deniedIframe')
                            ->setDispatched(false);
                    } elseif ($request->getParam('isAjax')) {
                        $request->setParam('forwarded', true)
                            ->setControllerName('index')
                            ->setActionName('deniedJson')
                            ->setDispatched(false);
                    } else {
                        $request->setParam('forwarded', true)
                            ->setRouteName('adminhtml')
                            ->setControllerName('index')
                            ->setActionName('login')
                            ->setDispatched(false);
                    }
                    return;
                }
            }
        }

        $session->refreshAcl();
    }

    /**
     * Unset session first visit flag after displaying page
     *
     * @deprecated after 1.4.0.1, logic moved to admin session
     * @param Varien_Event_Observer $event
     */
    public function actionPostDispatchAdmin($event)
    {
    }

    /**
     * Validate admin password and upgrade hash version
     *
     * @param Varien_Event_Observer $observer
     */
    public function actionAdminAuthenticate($observer)
    {
        $password = $observer->getEvent()->getPassword();
        $user = $observer->getEvent()->getUser();
        $authResult = $observer->getEvent()->getResult();

        if (!$authResult) {
            return;
        }

        if (
            !(bool) $user->getPasswordUpgraded()
            && !Mage::helper('core')->getEncryptor()->validateHashByVersion(
                $password,
                $user->getPassword(),
                Mage_Core_Model_Encryption::HASH_VERSION_SHA256
            )
        ) {
            $user
                ->setNewPassword($password)->setForceNewPassword(true)
                ->save();
            $user->setPasswordUpgraded(true);
        }
    }
}
