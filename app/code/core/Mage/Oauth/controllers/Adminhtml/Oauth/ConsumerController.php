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
 * Manage consumers controller
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Adminhtml_Oauth_ConsumerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Unset unused data from request
     * Skip getting "key" and "secret" because its generated from server side only
     *
     * @param array $data
     * @return array
     */
    protected function _filter(array $data)
    {
        foreach (['id', 'back', 'form_key', 'key', 'secret'] as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }
        return $data;
    }

    /**
     * Init titles
     *
     * @return $this
     */
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions(['delete']);
        $this->_title($this->__('System'))
            ->_title($this->__('OAuth'))
            ->_title($this->__('Consumers'));
        parent::preDispatch();
        return $this;
    }

    /**
     * Render grid page
     */
    public function indexAction()
    {
        $this->loadLayout();
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
     * Create page action
     */
    public function newAction()
    {
        /** @var Mage_Oauth_Model_Consumer $model */
        $model = Mage::getModel('oauth/consumer');

        $formData = $this->_getFormData();
        if ($formData) {
            $this->_setFormData($formData);
            $model->addData($formData);
        } else {
            /** @var Mage_Oauth_Helper_Data $helper */
            $helper = Mage::helper('oauth');
            $model->setKey($helper->generateConsumerKey());
            $model->setSecret($helper->generateConsumerSecret());
            $this->_setFormData($model->getData());
        }

        Mage::register('current_consumer', $model);

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Edit page action
     */
    public function editAction()
    {
        $id = (int) $this->getRequest()->getParam('id');

        if (!$id) {
            $this->_getSession()->addError(Mage::helper('oauth')->__('Invalid ID parameter.'));
            $this->_redirect('*/*/index');
            return;
        }

        /** @var Mage_Oauth_Model_Consumer $model */
        $model = Mage::getModel('oauth/consumer');
        $model->load($id);

        if (!$model->getId()) {
            $this->_getSession()->addError(Mage::helper('oauth')->__('Entry with ID #%s not found.', $id));
            $this->_redirect('*/*/index');
            return;
        }

        $model->addData($this->_filter($this->getRequest()->getParams()));
        Mage::register('current_consumer', $model);

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Render edit page
     */
    public function saveAction()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$this->_validateFormKey()) {
            if ($id) {
                $this->_redirect('*/*/edit', ['id' => $id]);
            } else {
                $this->_redirect('*/*/new', ['id' => $id]);
            }
            return;
        }

        $data = $this->_filter($this->getRequest()->getParams());

        //Validate current admin password
        $currentPassword = $this->getRequest()->getParam('current_password', null);
        $this->getRequest()->setParam('current_password', null);
        unset($data['current_password']);
        $result = $this->_validateCurrentPassword($currentPassword);

        if (is_array($result)) {
            foreach ($result as $error) {
                $this->_getSession()->addError($error);
            }
            if ($id) {
                $this->_redirect('*/*/edit', ['id' => $id]);
            } else {
                $this->_redirect('*/*/new');
            }
            return;
        }

        /** @var Mage_Oauth_Model_Consumer $model */
        $model = Mage::getModel('oauth/consumer');

        if ($id) {
            if (!(int) $id) {
                $this->_getSession()->addError(
                    $this->__('Invalid ID parameter.')
                );
                $this->_redirect('*/*/index');
                return;
            }
            $model->load($id);

            if (!$model->getId()) {
                $this->_getSession()->addError(
                    $this->__('Entry with ID #%s not found.', $id)
                );
                $this->_redirect('*/*/index');
                return;
            }
        } else {
            $dataForm = $this->_getFormData();
            if ($dataForm) {
                $data['key']    = $dataForm['key'];
                $data['secret'] = $dataForm['secret'];
            } else {
                // If an admin was started create a new consumer and at this moment he has been edited an existing
                // consumer, we save the new consumer with a new key-secret pair
                /** @var Mage_Oauth_Helper_Data $helper */
                $helper = Mage::helper('oauth');

                $data['key']    = $helper->generateConsumerKey();
                $data['secret'] = $helper->generateConsumerSecret();
            }
        }

        try {
            $model->addData($data);
            $model->save();
            $this->_getSession()->addSuccess($this->__('The consumer has been saved.'));
            $this->_setFormData(null);
        } catch (Mage_Core_Exception $e) {
            $this->_setFormData($data);
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
            $this->getRequest()->setParam('back', 'edit');
        } catch (Exception $e) {
            $this->_setFormData(null);
            Mage::logException($e);
            $this->_getSession()->addError($this->__('An error occurred on saving consumer data.'));
        }

        if ($this->getRequest()->getParam('back')) {
            if ($id || $model->getId()) {
                $this->_redirect('*/*/edit', ['id' => $model->getId()]);
            } else {
                $this->_redirect('*/*/new');
            }
        } else {
            $this->_redirect('*/*/index');
        }
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        $action = $this->getRequest()->getActionName();
        if ($action == 'index') {
            $action = null;
        } else {
            if ($action == 'new' || $action == 'save') {
                $action = 'edit';
            }
            $action = '/' . $action;
        }
        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        return $session->isAllowed('system/oauth/consumer' . $action);
    }

    /**
     * Get form data
     *
     * @return array
     */
    protected function _getFormData()
    {
        return $this->_getSession()->getData('consumer_data', true);
    }

    /**
     * Set form data
     *
     * @param array $data
     * @return $this
     */
    protected function _setFormData($data)
    {
        $this->_getSession()->setData('consumer_data', $data);
        return $this;
    }

    /**
     * Delete consumer action
     */
    public function deleteAction()
    {
        $consumerId = (int) $this->getRequest()->getParam('id');

        //Validate current admin password
        $currentPassword = $this->getRequest()->getParam('current_password', null);
        $this->getRequest()->setParam('current_password', null);
        $result = $this->_validateCurrentPassword($currentPassword);

        if (is_array($result)) {
            foreach ($result as $error) {
                $this->_getSession()->addError($error);
            }
            $this->_redirect('*/*/edit', ['id' => $consumerId]);
            return;
        }

        if ($consumerId) {
            try {
                /** @var Mage_Oauth_Model_Consumer $consumer */
                $consumer = Mage::getModel('oauth/consumer')->load($consumerId);
                if (!$consumer->getId()) {
                    Mage::throwException(Mage::helper('oauth')->__('Unable to find a consumer.'));
                }

                $consumer->delete();

                $this->_getSession()->addSuccess(Mage::helper('oauth')->__('The consumer has been deleted.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('oauth')->__('An error occurred while deleting the consumer.')
                );
            }
        }
        $this->_redirect('*/*/index');
    }
}
