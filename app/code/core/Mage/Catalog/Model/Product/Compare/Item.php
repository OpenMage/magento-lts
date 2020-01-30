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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Compare Item Model
 *
 * @method Mage_Catalog_Model_Resource_Product_Compare_Item getResource()
 * @method Mage_Catalog_Model_Product_Compare_Item setVisitorId(int $value)
 * @method Mage_Catalog_Model_Product_Compare_Item setCustomerId(int $value)
 * @method int getProductId()
 * @method Mage_Catalog_Model_Product_Compare_Item setProductId(int $value)
 * @method int getStoreId()
 * @method Mage_Catalog_Model_Product_Compare_Item setStoreId(int $value)
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
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

    /**
     * Initialize resourse model
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_compare_item');
    }

    /**
     * Retrieve Resource instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Compare_Item
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Set current store before save
     *
     * @return Mage_Catalog_Model_Product_Compare_Item
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
     * @return Mage_Catalog_Model_Product_Compare_Item
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
     * @param Mage_Customer_Model_Customer $customer
     * @return Mage_Catalog_Model_Product_Compare_Item
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
     * @return Mage_Catalog_Model_Product_Compare_Item
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
     * @return Mage_Catalog_Model_Product_Compare_Item
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
     * @return Mage_Catalog_Model_Product_Compare_Item
     */
    public function addProductData($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $this->setProductId($product->getId());
        }
        else if(intval($product)) {
            $this->setProductId(intval($product));
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
        $data = array();
        $data['customer_id'] = $this->getCustomerId();
        $data['visitor_id']  = $this->getVisitorId();
        $data['product_id']  = $this->getProductId();

        return $data;
    }

    /**
     * Customer login bind process
     *
     * @return Mage_Catalog_Model_Product_Compare_Item
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
     * @param Varien_Event_Observer $observer
     * @return Mage_Catalog_Model_Product_Compare_Item
     */
    public function bindCustomerLogout(Varien_Event_Observer $observer = null)
    {
        $this->_getResource()->purgeVisitorByCustomer($this);

        Mage::helper('catalog/product_compare')->calculate(true);
        return $this;
    }

    /**
     * Clean compare items
     *
     * @return Mage_Catalog_Model_Product_Compare_Item
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
