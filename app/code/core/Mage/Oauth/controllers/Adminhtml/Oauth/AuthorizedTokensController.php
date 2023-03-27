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
 * @package    Mage_Oauth
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Manage authorized tokens controller
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Adminhtml_Oauth_AuthorizedTokensController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init titles
     *
     * @return $this
     */
    public function preDispatch()
    {
        $this->_title($this->__('System'))
                ->_title($this->__('OAuth'))
                ->_title($this->__('Authorized Tokens'));
        parent::preDispatch();
        return $this;
    }

    /**
     * Render grid page
     */
    public function indexAction()
    {
        $this->loadLayout()->_setActiveMenu('system/oauth');
        $this->renderLayout();
    }

    /**
     * Render grid AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Update revoke status action
     */
    public function revokeAction()
    {
        $ids = $this->getRequest()->getParam('items');
        $status = $this->getRequest()->getParam('status');

        if (!is_array($ids) || !$ids) {
            // No rows selected
            $this->_getSession()->addError($this->__('Please select needed row(s).'));
            $this->_redirect('*/*/index');
            return;
        }

        if ($status === null) {
            // No status selected
            $this->_getSession()->addError($this->__('Please select revoke status.'));
            $this->_redirect('*/*/index');
            return;
        }

        try {
            /** @var Mage_Oauth_Model_Resource_Token_Collection $collection */
            $collection = Mage::getModel('oauth/token')->getCollection();
            $collection->joinConsumerAsApplication()
                    ->addFilterByType(Mage_Oauth_Model_Token::TYPE_ACCESS)
                    ->addFilterById($ids)
                    ->addFilterByRevoked(!$status);

            /** @var Mage_Oauth_Model_Token $item */
            foreach ($collection as $item) {
                $item->load($item->getId());
                $item->setRevoked($status)->save();

                $this->_sendTokenStatusChangeNotification($item, $status ? $this->__('revoked') : $this->__('enabled'));
            }
            if ($status) {
                $message = $this->__('Selected entries revoked.');
            } else {
                $message = $this->__('Selected entries enabled.');
            }
            $this->_getSession()->addSuccess($message);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('An error occurred on update revoke status.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        $ids = $this->getRequest()->getParam('items');

        if (!is_array($ids) || !$ids) {
            // No rows selected
            $this->_getSession()->addError($this->__('Please select needed row(s).'));
            $this->_redirect('*/*/index');
            return;
        }

        try {
            /** @var Mage_Oauth_Model_Resource_Token_Collection $collection */
            $collection = Mage::getModel('oauth/token')->getCollection();
            $collection->joinConsumerAsApplication()
                    ->addFilterByType(Mage_Oauth_Model_Token::TYPE_ACCESS)
                    ->addFilterById($ids);

            /** @var Mage_Oauth_Model_Token $item */
            foreach ($collection as $item) {
                $item->delete();

                $this->_sendTokenStatusChangeNotification($item, $this->__('deleted'));
            }
            $this->_getSession()->addSuccess($this->__('Selected entries has been deleted.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('An error occurred on delete action.'));
            Mage::logException($e);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        return $session->isAllowed('system/oauth/authorizedTokens');
    }

    /**
     * Send email notification to user about token status change
     *
     * @param Mage_Oauth_Model_Token $token Token object
     * @param string $newStatus Name of new token status
     */
    protected function _sendTokenStatusChangeNotification($token, $newStatus)
    {
        if (($adminId = $token->getAdminId())) {
            /** @var Mage_Admin_Model_Session $session */
            $session = Mage::getSingleton('admin/session');

            $admin = $session->getUser();

            if ($admin->getId() == $adminId) { // skip own tokens
                return;
            }
            $email = $admin->getEmail();
            $name  = $admin->getName(' ');
        } else {
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = Mage::getModel('customer/customer');

            $customer->load($token->getCustomerId());

            $email = $customer->getEmail();
            $name  = $customer->getName();
        }
        /** @var Mage_Oauth_Helper_Data $helper */
        $helper = Mage::helper('oauth');

        $helper->sendNotificationOnTokenStatusChange($email, $name, $token->getConsumer()->getName(), $newStatus);
    }
}
