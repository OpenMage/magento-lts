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
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer address
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Address extends Mage_Customer_Model_Address_Abstract
{
    protected $_customer;

    protected function _construct()
    {
        $this->_init('customer/address');
    }

    /**
     * Retrieve address customer identifier
     *
     * @return integer
     */
    public function getCustomerId()
    {
        return $this->_getData('customer_id') ? $this->_getData('customer_id') : $this->getParentId();
    }

    /**
     * Declare address customer identifier
     *
     * @param integer $id
     * @return Mage_Customer_Model_Address
     */
    public function setCustomerId($id)
    {
        $this->setParentId($id);
        $this->setData('customer_id', $id);
        return $this;
    }

    /**
     * Retrieve address customer
     *
     * @return Mage_Customer_Model_Customer | false
     */
    public function getCustomer()
    {
        if (!$this->getCustomerId()) {
            return false;
        }
        if (empty($this->_customer)) {
            $this->_customer = Mage::getModel('customer/customer')
                ->load($this->getCustomerId());
        }
        return $this->_customer;
    }

    /**
     * Delete customer address
     *
     * @return Mage_Customer_Model_Address
     */
    public function delete()
    {
        parent::delete();
        $this->setData(array());
        return $this;
    }

    /**
     * Retrieve address entity attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = $this->getData('attributes');
        if (is_null($attributes)) {
            $attributes = $this->_getResource()
                ->loadAllAttributes($this)
                ->getSortedAttributes();
            $this->setData('attributes', $attributes);
        }
        return $attributes;
    }

    public function __clone()
    {
        $this->setId(null);
    }
}
