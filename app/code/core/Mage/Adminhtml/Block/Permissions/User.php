<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml permissions user block
 *
 * @category   Mage
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
