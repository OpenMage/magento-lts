<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

/**
 * OAuth Consumer Edit Block
 *
 * @package    Mage_Oauth
 */
class Mage_Oauth_Block_Adminhtml_Oauth_Consumer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Consumer model
     *
     * @var Mage_Oauth_Model_Consumer
     */
    protected $_model;

    /**
     * Get consumer model
     *
     * @return Mage_Oauth_Model_Consumer
     */
    public function getModel()
    {
        if ($this->_model === null) {
            $this->_model = Mage::registry('current_consumer');
        }

        return $this->_model;
    }

    /**
     * Construct edit page
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'oauth';
        $this->_controller = 'adminhtml_oauth_consumer';
        $this->_mode = 'edit';

        $this->_addPreparedButton(
            id: self::BUTTON_TYPE_SAVE_EDIT,
            level: 100,
            module: 'oauth',
        );

        $this->_formScripts[] = 'function saveAndContinueEdit()'
        . "{editForm.submit($('edit_form').action + 'back/edit/')}";

        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'label', $this->__('Save'));
        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'id', 'save_button');
        $this->_updateButton(self::BUTTON_TYPE_DELETE, 'label', $this->__('Delete'));
        $this->_updateButton(self::BUTTON_TYPE_DELETE, 'onclick', "if(confirm('" . Mage::helper('core')->jsQuoteEscape(
            Mage::helper('adminhtml')->__('Are you sure you want to do this?'),
        ) . "')) editForm.submit('" . $this->getUrl('*/*/delete') . "'); return false;");

        /** @var Mage_Admin_Model_Session $session */
        $session = Mage::getSingleton('admin/session');
        if (!$this->getModel() || !$this->getModel()->getId() || !$session->isAllowed('system/oauth/consumer/delete')) {
            $this->_removeButton('delete');
        }
    }

    /**
     * Get header text
     *
     * @return string
     */
    #[Override]
    public function getHeaderText()
    {
        if ($this->getModel()->getId()) {
            return $this->__('Edit Consumer');
        }

        return $this->__('New Consumer');
    }
}
