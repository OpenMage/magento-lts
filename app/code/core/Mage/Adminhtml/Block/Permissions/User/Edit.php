<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml permissions user edit page
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_User_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'user_id';
        $this->_controller = 'permissions_user';

        parent::__construct();

        $this->_updateButton(self::BUTTON_TYPE_SAVE, 'label', Mage::helper('adminhtml')->__('Save'));
        $this->_updateButton(self::BUTTON_TYPE_DELETE, 'label', Mage::helper('adminhtml')->__('Delete'));
        $this->_updateButton(self::BUTTON_TYPE_DELETE, 'onclick', "if(confirm('" . Mage::helper('core')->jsQuoteEscape(
            Mage::helper('adminhtml')->__('Are you sure you want to do this?'),
        ) . "')) editForm.submit('" . $this->getUrl('*/*/delete') . "'); return false;");
    }

    /**
     * @return string
     */
    #[Override]
    public function getHeaderText()
    {
        if (Mage::registry('permissions_user')->getId()) {
            return Mage::helper('adminhtml')->__("Edit User '%s'", $this->escapeHtml(Mage::registry('permissions_user')->getUsername()));
        }

        return Mage::helper('adminhtml')->__('New User');
    }
}
