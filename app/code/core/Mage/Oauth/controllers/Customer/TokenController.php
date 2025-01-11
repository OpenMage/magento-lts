<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * oAuth My Applications controller
 *
 * Tab "My Applications" in the Customer Account
 *
 * @category   Mage
 * @package    Mage_Oauth
 */
class Mage_Oauth_Customer_TokenController extends Mage_Core_Controller_Front_Action
{
    /**
     * Customer session model
     *
     * @var Mage_Customer_Model_Session
     */
    protected $_session;

    /**
     * Customer session model
     *
     * @var string
     */
    protected $_sessionName = 'customer/session';

    /**
     * Check authentication
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        /** @var Mage_Customer_Model_Session $classInstance */
        $classInstance = Mage::getSingleton($this->_sessionName);
        $this->_session = $classInstance;
        if (!$this->_session->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        return $this;
    }

    /**
     * Render grid page
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();
    }

    /**
     * Redirect to referrer URL or otherwise to index page without params
     *
     * @return $this
     */
    protected function _redirectBack()
    {
        $url = $this->_getRefererUrl();
        if (Mage::app()->getStore()->getBaseUrl() == $url) {
            $url = Mage::getUrl('*/*/index');
        }
        $this->_redirectUrl($url);
        return $this;
    }

    /**
     * Update revoke status action
     */
    public function revokeAction()
    {
        $id = $this->getRequest()->getParam('id');
        $status = $this->getRequest()->getParam('status');

        if ((int) $id === 0) {
            // No ID
            $this->_session->addError($this->__('Invalid entry ID.'));
            $this->_redirectBack();
            return;
        }

        if ($status === null) {
            // No status selected
            $this->_session->addError($this->__('Invalid revoke status.'));
            $this->_redirectBack();
            return;
        }

        try {
            /** @var Mage_Oauth_Model_Resource_Token_Collection $collection */
            $collection = Mage::getModel('oauth/token')->getCollection();
            $collection->joinConsumerAsApplication()
                    ->addFilterByCustomerId($this->_session->getCustomerId())
                    ->addFilterById($id)
                    ->addFilterByType(Mage_Oauth_Model_Token::TYPE_ACCESS)
                    ->addFilterByRevoked(!$status);
            //here is can be load from model, but used from collection for get consumer name

            /** @var Mage_Oauth_Model_Token $model */
            $model = $collection->getFirstItem();
            if ($model->getId()) {
                $name = $model->getName();
                $model->load($model->getId());
                $model->setRevoked($status)->save();
                if ($status) {
                    $message = $this->__('Application "%s" has been revoked.', $name);
                } else {
                    $message = $this->__('Application "%s" has been enabled.', $name);
                }
                $this->_session->addSuccess($message);
            } else {
                $this->_session->addError($this->__('Application not found.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_session->addError($this->__('An error occurred on update revoke status.'));
            Mage::logException($e);
        }
        $this->_redirectBack();
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');

        if ((int) $id === 0) {
            // No ID
            $this->_session->addError($this->__('Invalid entry ID.'));
            $this->_redirectBack();
            return;
        }

        try {
            /** @var Mage_Oauth_Model_Resource_Token_Collection $collection */
            $collection = Mage::getModel('oauth/token')->getCollection();
            $collection->joinConsumerAsApplication()
                    ->addFilterByCustomerId($this->_session->getCustomerId())
                    ->addFilterByType(Mage_Oauth_Model_Token::TYPE_ACCESS)
                    ->addFilterById($id);

            /** @var Mage_Oauth_Model_Token $model */
            $model = $collection->getFirstItem();
            if ($model->getId()) {
                $name = $model->getName();
                $model->delete();
                $this->_session->addSuccess(
                    $this->__('Application "%s" has been deleted.', $name),
                );
            } else {
                $this->_session->addError($this->__('Application not found.'));
            }
        } catch (Mage_Core_Exception $e) {
            $this->_session->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_session->addError($this->__('An error occurred on delete application.'));
            Mage::logException($e);
        }
        $this->_redirectBack();
    }
}
