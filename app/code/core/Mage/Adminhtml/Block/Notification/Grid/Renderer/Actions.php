<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml AdminNotification Severity Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Notification_Grid_Renderer_Actions
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if (!$row->getIsRead()) {
            return sprintf('<a target="_blank" href="%s">%s</a> | <a href="%s">%s</a> | <a href="%s" onClick="deleteConfirm(\'%s\',this.href); return false;">%s</a>',
                $row->getUrl(),
                Mage::helper('adminnotification')->__('Read Details'),
                $this->getUrl('*/*/markAsRead/', array('_current'=>true, 'id' => $row->getId())),
                Mage::helper('adminnotification')->__('Mark as Read'),
                $this->getUrl('*/*/remove/', array('_current'=>true, 'id' => $row->getId())),
                Mage::helper('adminnotification')->__('Are you sure?'),
                Mage::helper('adminnotification')->__('Remove')
            );
        }
        else {
            return sprintf('<a target="_blank" href="%s">%s</a> | <a href="%s" onClick="deleteConfirm(\'%s\',this.href); return false;">%s</a>',
                $row->getUrl(),
                Mage::helper('adminnotification')->__('Read Details'),
                $this->getUrl('*/*/remove/', array('_current'=>true, 'id' => $row->getId())),
                Mage::helper('adminnotification')->__('Are you sure?'),
                Mage::helper('adminnotification')->__('Remove')
            );
        }
    }
}