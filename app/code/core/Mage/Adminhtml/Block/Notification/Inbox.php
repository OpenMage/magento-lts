<?php
/**
 * Adminhtml AdminNotification inbox grid
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Notification_Inbox extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'notification';
        $this->_headerText = Mage::helper('adminnotification')->__('Messages Inbox');
        parent::__construct();
    }

    protected function _prepareLayout()
    {
        $this->_removeButton('add');

        return parent::_prepareLayout();
    }
}
