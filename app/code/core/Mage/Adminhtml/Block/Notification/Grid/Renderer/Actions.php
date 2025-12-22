<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml AdminNotification Severity Renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Notification_Grid_Renderer_Actions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $escapedRowUrl =  $this->escapeUrl($row->getUrl());
        $readDetailsHtml = ($escapedRowUrl)
            ? '<a target="_blank" href="' . $escapedRowUrl . '">'
                . $this->escapeHtml(Mage::helper('adminnotification')->__('Read Details')) . '</a> | '
            : '';

        $markAsReadHtml = ($row->getIsRead())
            ? ''
            : '<a href="' . $this->getUrl('*/*/markAsRead/', ['_current' => true, 'id' => $row->getId()]) . '">'
                . $this->escapeHtml(Mage::helper('adminnotification')->__('Mark as Read')) . '</a> | ';

        $deleteConfirmHtml = sprintf(
            "deleteConfirm('%s', this.href)",
            Mage::helper('core')->jsQuoteEscape(Mage::helper('adminnotification')->__('Are you sure?')),
        );

        /** @var Mage_Core_Helper_Url $helper */
        $helper = $this->helper('core/url');
        return sprintf(
            '%s%s<a href="%s" onClick="%s; return false;">%s</a>',
            $readDetailsHtml,
            $markAsReadHtml,
            $this->getUrl('*/*/remove/', [
                '_current' => true,
                'id' => $row->getId(),
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $helper->getEncodedUrl()]),
            $deleteConfirmHtml,
            $this->escapeHtml(Mage::helper('adminnotification')->__('Remove')),
        );
    }
}
