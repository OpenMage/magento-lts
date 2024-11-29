<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml AdminNotification Severity Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Notification_Grid_Renderer_Severity extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @return  string
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
