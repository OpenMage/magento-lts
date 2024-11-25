<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin observer model
 *
 * @category   Mage
 * @package    Mage_Admin
 */
class Mage_Admin_Model_Observer
{
    public const FLAG_NO_LOGIN = 'no-login';

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
            'refresh', // captcha refresh
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

                    if ($coreSession->validateFormKey($request->getPost('form_key'))) {
                        $postLogin = $request->getPost('login');
                        $username = $postLogin['username'] ?? '';
                        $password = $postLogin['password'] ?? '';
                        $session->login($username, $password, $request);
                        $request->setPost('login', null);
                    } else {
                        if (!$request->getParam('messageSent')) {
                            Mage::getSingleton('adminhtml/session')->addError(
                                Mage::helper('adminhtml')->__('Invalid Form Key. Please refresh the page.'),
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
    public function actionPostDispatchAdmin($event) {}

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

        if (!(bool) $user->getPasswordUpgraded()
            && !Mage::helper('core')->getEncryptor()->validateHashByVersion(
                $password,
                $user->getPassword(),
                Mage_Core_Model_Encryption::HASH_VERSION_SHA256,
            )
        ) {
            $user
                ->setNewPassword($password)->setForceNewPassword(true)
                ->save();
            $user->setPasswordUpgraded(true);
        }
    }
}
