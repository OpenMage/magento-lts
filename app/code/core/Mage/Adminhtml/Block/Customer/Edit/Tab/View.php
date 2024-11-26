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
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_View extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    protected $_customerLog;

    /**
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = Mage::registry('current_customer');
        }
        return $this->_customer;
    }

    /**
     * @return string|void
     */
    public function getGroupName()
    {
        if ($groupId = $this->getCustomer()->getGroupId()) {
            return Mage::getModel('customer/group')
                ->load($groupId)
                ->getCustomerGroupCode();
        }
    }

    /**
     * Load Customer Log model
     *
     * @return Mage_Log_Model_Customer
     */
    public function getCustomerLog()
    {
        if (!$this->_customerLog) {
            $this->_customerLog = Mage::getModel('log/customer')
                ->loadByCustomer($this->getCustomer()->getId());
        }
        return $this->_customerLog;
    }

    /**
     * Get customer creation date
     *
     * @return string
     */
    public function getCreateDate()
    {
        return ($date = $this->getCustomer()->getCreatedAt())
            ? $this->formatTimezoneDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true, false)
            : null;
    }

    /**
     * @return string|null
     */
    public function getStoreCreateDate()
    {
        $date = $this->getCustomer()->getCreatedAtTimestamp();
        if (!$date) {
            return null;
        }

        return $this->formatTimezoneDate(
            $date,
            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
            true,
            false
        );
    }

    public function getStoreCreateDateTimezone()
    {
        return Mage::app()->getStore($this->getCustomer()->getStoreId())
            ->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
    }

    /**
     * Get customer last login date
     *
     * @return string
     */
    public function getLastLoginDate()
    {
        return ($date = $this->getCustomerLog()->getLoginAtTimestamp())
            ? $this->formatTimezoneDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true, false)
            : Mage::helper('customer')->__('Never');
    }

    /**
     * @return string
     */
    public function getStoreLastLoginDate()
    {
        if ($date = $this->getCustomerLog()->getLoginAtTimestamp()) {
            $date = Mage::app()->getLocale()->storeDate(
                $this->getCustomer()->getStoreId(),
                $date,
                true
            );
            return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
        }
        return Mage::helper('customer')->__('Never');
    }

    public function getStoreLastLoginDateTimezone()
    {
        return Mage::app()->getStore($this->getCustomer()->getStoreId())
            ->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
    }

    /**
     * @return string
     */
    public function getCurrentStatus()
    {
        $log = $this->getCustomerLog();
        if ($log->getLogoutAt()
            || !$log->getLastVisitAt()
            || strtotime(Varien_Date::now()) - strtotime($log->getLastVisitAt()) > Mage_Log_Model_Visitor::getOnlineMinutesInterval() * 60
        ) {
            return Mage::helper('customer')->__('Offline');
        }
        return Mage::helper('customer')->__('Online');
    }

    /**
     * @return string
     */
    public function getIsConfirmedStatus()
    {
        $this->getCustomer();
        if (!$this->_customer->getConfirmation()) {
            return Mage::helper('customer')->__('Confirmed');
        }
        if ($this->_customer->isConfirmationRequired()) {
            return Mage::helper('customer')->__('Not confirmed, cannot login');
        }
        return Mage::helper('customer')->__('Not confirmed, can login');
    }

    public function getCreatedInStore()
    {
        return Mage::app()->getStore($this->getCustomer()->getStoreId())->getName();
    }

    public function getStoreId()
    {
        return $this->getCustomer()->getStoreId();
    }

    /**
     * @return string
     */
    public function getBillingAddressHtml()
    {
        $html = '';
        if ($address = $this->getCustomer()->getPrimaryBillingAddress()) {
            $html = $address->format('html');
        } else {
            $html = Mage::helper('customer')->__('The customer does not have default billing address.');
        }
        return $html;
    }

    /**
     * @return string
     */
    public function getAccordionHtml()
    {
        return $this->getChildHtml('accordion');
    }

    /**
     * @return string
     */
    public function getSalesHtml()
    {
        return $this->getChildHtml('sales');
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('customer')->__('Customer View');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('customer')->__('Customer View');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if (Mage::registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if (Mage::registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }

    /**
     * Return instance of core helper
     *
     * @deprecated
     * @return Mage_Core_Helper_Data
     */
    protected function _getCoreHelper()
    {
        return Mage::helper('core');
    }
}
