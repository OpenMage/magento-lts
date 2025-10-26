<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml permissions block edit page
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Block_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'block_id';
        $this->_controller = 'permissions_block';

        parent::__construct();
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
