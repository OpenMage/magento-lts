<?php
/**
 * Adminhtml permissions block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Permissions_Block extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'permissions_block';
        $this->_headerText = Mage::helper('adminhtml')->__('Blocks');
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add New Block');
        parent::__construct();
    }

    /**
     * Prepare output HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        Mage::dispatchEvent('permissions_block_html_before', ['block' => $this]);
        return parent::_toHtml();
    }
}
