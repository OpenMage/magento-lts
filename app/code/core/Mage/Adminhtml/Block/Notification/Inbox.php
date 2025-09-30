<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml AdminNotification inbox grid
 *
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
