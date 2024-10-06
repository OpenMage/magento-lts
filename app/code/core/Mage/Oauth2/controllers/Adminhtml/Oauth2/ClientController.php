<?php

/**
 * OAuth2 Client Controller for Magento Admin Panel
 */
class Mage_Oauth2_Adminhtml_Oauth2_ClientController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Mage_Oauth2_Model_Client
     */
    protected $_clientModel;

    /**
     * @var Mage_Adminhtml_Model_Session
     */
    protected $_session;

    /**
     * Initialize client model
     *
     * @return Mage_Oauth2_Model_Client
     */
    protected function _initClientModel()
    {
        if ($this->_clientModel === null) {
            $this->_clientModel = Mage::getModel('oauth2/client');
        }
        return $this->_clientModel;
    }

    /**
     * Get admin session
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        if ($this->_session === null) {
            $this->_session = Mage::getSingleton('adminhtml/session');
        }
        return $this->_session;
    }

    /**
     * Pre-dispatch actions
     *
     * @return Mage_Oauth2_Adminhtml_Oauth2_ClientController
     */
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions(['delete']);
        $this->_title($this->__('System'))
            ->_title($this->__('OAuth2'))
            ->_title($this->__('Clients'));
        return parent::preDispatch();
    }

    /**
     * Index action - display list of OAuth2 clients
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_addContent($this->getLayout()->createBlock('oauth2/adminhtml_client'))
            ->renderLayout();
    }

    /**
     * New client action - display form for creating new OAuth2 client
     */
    public function newAction()
    {
        $model = $this->_initClientModel();
        $formData = $this->_getFormData();

        if ($formData) {
            $model->addData($formData);
        } else {
            $model->setSecret(Mage::helper('oauth2')->generateClientSecret());
        }

        $this->_setFormData($formData ?: $model->getData());
        Mage::register('current_oauth2_client', $model);

        $this->loadLayout()
            ->_addContent($this->getLayout()->createBlock('oauth2/adminhtml_client_edit'))
            ->renderLayout();
    }

    /**
     * Edit client action - display form for editing existing OAuth2 client
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_initClientModel()->load($id);

        if ($model->getId() || $id == 0) {
            Mage::register('current_oauth2_client', $model);
            $this->loadLayout()
                ->_addContent($this->getLayout()->createBlock('oauth2/adminhtml_client_edit'))
                ->renderLayout();
        } else {
            $this->_getSession()->addError(Mage::helper('oauth2')->__('Client does not exist'));
            $this->_redirect('*/*/');
        }
    }

    /**
     * Save client action - save new or update existing OAuth2 client
     */
    public function saveAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirectToFormPage();
        }

        $data = $this->_filter($this->getRequest()->getParams());
        $id = $this->getRequest()->getParam('id');

        if (!$this->_validateCurrentPassword($this->getRequest()->getParam('current_password'))) {
            return $this->_redirectToFormPage($id);
        }

        $model = $this->_initClientModel();

        if ($id) {
            if (!$this->_loadModelById($model, $id)) {
                return;
            }
        } else {
            $data['secret'] = $this->_getFormData()['secret'] ?? Mage::helper('oauth2')->generateClientSecret();
        }

        try {
            $model->addData($data)->save();
            $this->_getSession()->addSuccess($this->__('The client has been saved.'));
            $this->_setFormData(null);
        } catch (Mage_Core_Exception $e) {
            $this->_handleSaveException($e, $data);
        } catch (Exception $e) {
            $this->_handleSaveException($e);
        }

        $this->_redirectAfterSave($model);
    }

    /**
     * Delete client action
     */
    public function deleteAction()
    {
        $clientId = $this->getRequest()->getParam('id');

        if ($clientId) {
            try {
                $this->_initClientModel()->load($clientId)->delete();
                $this->_getSession()->addSuccess('Client deleted.');
            } catch (Exception $e) {
                $this->_getSession()->addError('Error: ' . $e->getMessage());
            }
        } else {
            $this->_getSession()->addError('Unable to find client to delete.');
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Get form data from session
     *
     * @return mixed
     */
    protected function _getFormData()
    {
        return $this->_getSession()->getData('oauth2_client_data', true);
    }

    /**
     * Set form data to session
     *
     * @param mixed $data
     */
    protected function _setFormData($data)
    {
        $this->_getSession()->setData('oauth2_client_data', $data);
    }

    /**
     * Filter input data
     *
     * @param array $data
     * @return array
     */
    protected function _filter(array $data)
    {
        $fieldsToRemove = ['id', 'back', 'form_key', 'secret'];
        foreach ($fieldsToRemove as $field) {
            unset($data[$field]);
        }

        if (isset($data['grant_types'])) {
            $data['grant_types'] = implode(',', $data['grant_types']);
        }

        return $data;
    }

    /**
     * Redirect to appropriate form page
     *
     * @param int|null $id
     */
    private function _redirectToFormPage($id = null)
    {
        $this->_redirect($id ? '*/*/edit' : '*/*/new', ['id' => $id]);
    }

    /**
     * Load model by ID and validate its existence
     *
     * @param Mage_Core_Model_Abstract $model
     * @param int $id
     * @return bool
     */
    private function _loadModelById($model, $id)
    {
        if (!(int) $id) {
            $this->_getSession()->addError($this->__('Invalid ID parameter.'));
            $this->_redirect('*/*/index');
            return false;
        }

        $model->load($id);

        if (!$model->getId()) {
            $this->_getSession()->addError($this->__('Entry with ID #%s not found.', $id));
            $this->_redirect('*/*/index');
            return false;
        }

        return true;
    }

    /**
     * Handle exceptions during save action
     *
     * @param Exception $e
     * @param array|null $data
     */
    private function _handleSaveException($e, $data = null)
    {
        if ($data !== null) {
            $this->_setFormData($data);
        }
        $this->_setFormData(null);
        $message = $e instanceof Mage_Core_Exception ? Mage::helper('core')->escapeHtml($e->getMessage()) : $this->__('An error occurred on saving client data.');
        $this->_getSession()->addError($message);
        $this->getRequest()->setParam('back', 'edit');

        if ($e instanceof Exception && !($e instanceof Mage_Core_Exception)) {
            Mage::logException($e);
        }
    }

    /**
     * Redirect after save action
     *
     * @param Mage_Core_Model_Abstract $model
     */
    private function _redirectAfterSave($model)
    {
        if ($this->getRequest()->getParam('back')) {
            $this->_redirect('*/*/edit', ['id' => $model->getId()]);
        } else {
            $this->_redirect('*/*/index');
        }
    }
}
