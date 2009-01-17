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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer session model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Session extends Mage_Core_Model_Session_Abstract
{
    protected $_customer;

    public function __construct()
    {
        $this->init('customer');
        Mage::dispatchEvent('customer_session_init', array('customer_session'=>$this));
    }

    /**
     * Set customer object and setting customer id in session
     *
     * @param   Mage_Customer_Model_Customer $customer
     * @return  Mage_Customer_Model_Session
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        // check if customer is not confirmed
        if ($customer->isConfirmationRequired()) {
            if ($customer->getConfirmation()) {
                throw new Exception('This customer is not confirmed and cannot log in.',
                    Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED
                );
            }
        }
        $this->_customer = $customer;
        $this->setId($customer->getId());
        // save customer as confirmed, if it is not
        if ((!$customer->isConfirmationRequired()) && $customer->getConfirmation()) {
            $customer->setConfirmation(null)->save();
            $customer->setIsJustConfirmed(true);
        }
        return $this;
    }

    /**
     * Retrieve costomer model object
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if ($this->_customer instanceof Mage_Customer_Model_Customer) {
            return $this->_customer;
        }

        $customer = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
        if ($this->getId()) {
            $customer->load($this->getId());
        }

        $this->setCustomer($customer);
        return $this->_customer;
    }

    /**
     * Retrieve customer id from current session
     *
     * @return int || null
     */
    public function getCustomerId()
    {
        return $this->getId();
    }

    /**
     * Get customer group id
     * If customer is not logged in, the default customer group id from config will be returned
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->getCustomer()->getGroupId();
    }

    /**
     * Checking custommer loggin status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return (bool)$this->getId() && (bool)$this->getCustomer()->getId();
    }

    /**
     * Customer authorization
     *
     * @param   string $username
     * @param   string $password
     * @return  bool
     */
    public function login($username, $password)
    {
        $customer = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());

        if ($customer->authenticate($username, $password)) {
            $this->setCustomer($customer);
            Mage::dispatchEvent('customer_login', array('customer'=>$customer));
            return true;
        }
        return false;
    }

    public function setCustomerAsLoggedIn($customer)
    {
        $this->setCustomer($customer);
        Mage::dispatchEvent('customer_login', array('customer'=>$customer));
        return $this;
    }

    /**
     * Authorization customer by identifier
     *
     * @param   int $customerId
     * @return  bool
     */
    public function loginById($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        if ($customer) {
            $this->setCustomer($customer);
            Mage::dispatchEvent('customer_login', array('customer'=>$customer));
            return true;
        }
        return false;
    }

    /**
     * Logout customer
     *
     * @return Mage_Customer_Model_Session
     */
    public function logout()
    {
        if ($this->isLoggedIn()) {
            Mage::dispatchEvent('customer_logout', array('customer' => $this->getCustomer()) );
            $this->setId(null);
        }
        return $this;
    }

    /**
     * Authenticate controller action by login customer
     *
     * @param   Mage_Core_Controller_Varien_Action $action
     * @return  bool
     */
    public function authenticate(Mage_Core_Controller_Varien_Action $action, $loginUrl = null)
    {
        if (!$this->isLoggedIn()) {
            $this->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_current'=>true)));
            if (is_null($loginUrl)) {
                $loginUrl = Mage::helper('customer')->getLoginUrl();
            }
            $action->getResponse()->setRedirect($loginUrl);
            return false;
        }
        return true;
    }
}
