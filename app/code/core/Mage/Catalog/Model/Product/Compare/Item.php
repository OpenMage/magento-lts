<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Compare Item Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Resource_Product_Compare_Item _getResource()
 * @method Mage_Catalog_Model_Resource_Product_Compare_Item getResource()
 *
 * @method $this setVisitorId(int $value)
 * @method $this setCustomerId(int $value)
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method bool hasVisitorId()
 * @method bool hasCustomerId()
 * @method bool hasStoreId()
 */
class Mage_Catalog_Model_Product_Compare_Item extends Mage_Core_Model_Abstract
{
    /**
     * Model cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'catalog_compare_item';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'catalog_compare_item';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getItem() in this case
     *
     * @var string
     */
    protected $_eventObject = 'item';

    protected function _construct()
    {
        $this->_init('catalog/product_compare_item');
    }

    /**
     * Set current store before save
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->hasStoreId()) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        return $this;
    }

    /**
     * Save object data
     *
     * @return $this
     */
    public function save()
    {
        if ($this->hasCustomerId() || $this->hasVisitorId()) {
            parent::save();
        }
        return $this;
    }

    /**
     * Add customer data from customer object
     *
     * @return $this
     */
    public function addCustomerData(Mage_Customer_Model_Customer $customer)
    {
        $this->setCustomerId($customer->getId());
        return $this;
    }

    /**
     * Set visitor
     *
     * @param int $visitorId
     * @return $this
     */
    public function addVisitorId($visitorId)
    {
        $this->setVisitorId($visitorId);
        return $this;
    }

    /**
     * Load compare item by product
     *
     * @param mixed $product
     * @return $this
     */
    public function loadByProduct($product)
    {
        $this->_getResource()->loadByProduct($this, $product);
        return $this;
    }

    /**
     * Set product data
     *
     * @param mixed $product
     * @return $this
     */
    public function addProductData($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $this->setProductId($product->getId());
        } elseif ((int) $product) {
            $this->setProductId((int) $product);
        }

        return $this;
    }

    /**
     * Retrieve data for save
     *
     * @return array
     */
    public function getDataForSave()
    {
        $data = [];
        $data['customer_id'] = $this->getCustomerId();
        $data['visitor_id']  = $this->getVisitorId();
        $data['product_id']  = $this->getProductId();

        return $data;
    }

    /**
     * Customer login bind process
     *
     * @return $this
     */
    public function bindCustomerLogin()
    {
        $this->_getResource()->updateCustomerFromVisitor($this);

        Mage::helper('catalog/product_compare')->setCustomerId($this->getCustomerId())->calculate();
        return $this;
    }

    /**
     * Customer logout bind process
     *
     * @return $this
     */
    public function bindCustomerLogout(?Varien_Event_Observer $observer = null)
    {
        $this->_getResource()->purgeVisitorByCustomer($this);

        Mage::helper('catalog/product_compare')->calculate(true);
        return $this;
    }

    /**
     * Clean compare items
     *
     * @return $this
     */
    public function clean()
    {
        $this->_getResource()->clean($this);
        return $this;
    }

    /**
     * Retrieve Customer Id if loggined
     *
     * @return int
     */
    public function getCustomerId()
    {
        if (!$this->hasData('customer_id')) {
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $this->setData('customer_id', $customerId);
        }
        return $this->getData('customer_id');
    }

    /**
     * Retrieve Visitor Id
     *
     * @return int
     */
    public function getVisitorId()
    {
        if (!$this->hasData('visitor_id')) {
            $visitorId = Mage::getSingleton('log/visitor')->getId();
            $this->setData('visitor_id', $visitorId);
        }
        return $this->getData('visitor_id');
    }
}
