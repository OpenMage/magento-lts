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
class Mage_Adminhtml_Block_Notification_Grid_Renderer_Severity extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $notice = Mage::getSingleton('adminnotification/inbox');

        switch ($row->getData($this->getColumn()->getIndex())) {
            case Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL:
                $class = 'critical';
                $value = $notice->getSeverities(Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL);
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR:
                $class = 'major';
                $value = $notice->getSeverities(Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR);
                break;
            case Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR:
                $class = 'minor';
                $value = $notice->getSeverities(Mage_AdminNotification_Model_Inbox::SEVERITY_MINOR);
                break;
            default:
            case Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE:
                $class = 'notice';
                $value = $notice->getSeverities(Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE);
                break;
        }

        return '<span class="grid-severity-' . $class . '"><span>' . $value . '</span></span>';
    }
}
