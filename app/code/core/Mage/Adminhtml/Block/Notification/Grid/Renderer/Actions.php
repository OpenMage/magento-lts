<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml AdminNotification Severity Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Notification_Grid_Renderer_Actions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $readDetailsHtml = ($row->getUrl())
            ? '<a target="_blank" href="' . $row->getUrl() . '">' .
                Mage::helper('adminnotification')->__('Read Details') . '</a> | '
            : '';

        $markAsReadHtml = (!$row->getIsRead())
            ? '<a href="' . $this->getUrl('*/*/markAsRead/', ['_current' => true, 'id' => $row->getId()]) . '">' .
                Mage::helper('adminnotification')->__('Mark as Read') . '</a> | '
            : '';

        /** @var Mage_Core_Helper_Url $helper */
        $helper = $this->helper('core/url');
        return sprintf(
            '%s%s<a href="%s" onClick="deleteConfirm(\'%s\', this.href); return false;">%s</a>',
            $readDetailsHtml,
            $markAsReadHtml,
            $this->getUrl('*/*/remove/', [
                '_current' => true,
                'id' => $row->getId(),
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $helper->getEncodedUrl()]),
            Mage::helper('adminnotification')->__('Are you sure?'),
            Mage::helper('adminnotification')->__('Remove'),
        );
    }
}
