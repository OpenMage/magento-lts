<?php

/**
 * Adminhtml permissions user block
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_User extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'permissions_user';
        $this->_headerText = Mage::helper('adminhtml')->__('Users');
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add New User');
        parent::__construct();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('permissions_user_html_before', ['block' => $this]);
        return parent::_toHtml();
    }
}
