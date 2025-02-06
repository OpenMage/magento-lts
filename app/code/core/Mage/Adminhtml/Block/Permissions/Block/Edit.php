<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml permissions block edit page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Block_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'block_id';
        $this->_controller = 'permissions_block';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save Block'));
        $this->_updateButton('delete', 'label', Mage::helper('adminhtml')->__('Delete Block'));
    }

    /**
     * Return text that to be placed to block header
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('permissions_block')->getId()) {
            return Mage::helper('adminhtml')->__("Edit Block '%s'", $this->escapeHtml(Mage::registry('permissions_block')->getBlockName()));
        }
        return Mage::helper('adminhtml')->__('New block');
    }
}
