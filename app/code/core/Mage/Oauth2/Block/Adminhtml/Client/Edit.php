<?php

class Mage_Oauth2_Block_Adminhtml_Client_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_model;

    /**
     * Constructs the object and initializes the block group, controller, and mode.
     * Updates the save and delete buttons with localized labels.
     * Removes the delete button if the user is not allowed to perform the delete action.
     * Adds a save and continue button with a localized label and onclick event.
     * Adds a form script to submit the form with a specific action.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'oauth2';
        $this->_controller = 'adminhtml_client';
        $this->_mode = 'edit';

        $this->_updateButton('save', 'label', Mage::helper('oauth')->__('Save'));
        $this->_updateButton('save', 'id', 'save_button');
        $this->_updateButton('delete', 'label', Mage::helper('oauth')->__('Delete'));
        $this->_updateButton('delete', 'onclick', 'if(confirm(\'' . Mage::helper('core')->jsQuoteEscape(
            Mage::helper('adminhtml')->__('Are you sure you want to do this?')
        ) . '\')) editForm.submit(\'' . $this->getUrl('*/*/delete', ['id' => $this->getModel()->getId()]) . '\'); return false;');

        if (!$this->_isAllowedAction('delete')) {
            $this->_removeButton('delete');
        }

        $this->_addButton('save_and_continue', [
            'label'     => Mage::helper('oauth')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class' => 'save'
        ], 100);

        $this->_formScripts[] = "function saveAndContinueEdit()" .
            "{editForm.submit($('edit_form').action + 'back/edit/');}";
    }

    /**
     * Prepares the layout for the block.
     *
     */
    public function getHeaderText()
    {
        return $this->getModel()->getId()
            ? $this->__("Edit Client '%s'", $this->escapeHtml($this->getModel()->getName()))
            : $this->__('New Client');
    }

    /**
     * Check if the current user is allowed to perform the specified action.
     *
     * @param string $action The action to check.
     * @return bool Returns true if the user is allowed, false otherwise.
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/oauth2/client/' . $action);
    }

    /**
     * Retrieves the model object from the registry if it is not already set.
     *
     * @return mixed The model object from the registry.
     */
    protected function getModel()
    {
        if (null === $this->_model) {
            $this->_model = Mage::registry('current_oauth2_client');
        }
        return $this->_model;
    }
}
