<?php
class Mage_Paypal_Block_Adminhtml_Debug extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'paypal';
        $this->_controller = 'adminhtml_debug';
        $this->_headerText = Mage::helper('paypal')->__('PayPal Debug Log');
        parent::__construct();
        $this->_removeButton('add');
        
        $this->_addButton('delete_all', [
            'label'   => Mage::helper('paypal')->__('Delete All Logs'),
            'onclick' => 'deleteConfirm(\'' . 
                Mage::helper('paypal')->__('Are you sure you want to delete all debug logs?') . 
                '\', \'' . $this->getUrl('*/*/deleteAll') . '\')',
            'class'   => 'delete'
        ]);
    }
}
