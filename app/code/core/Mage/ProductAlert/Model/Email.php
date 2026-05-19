<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ProductAlert
 */

/**
 * ProductAlert Email processor
 *
 * @package    Mage_ProductAlert
 */
class Mage_ProductAlert_Model_Email extends Mage_Core_Model_Abstract
{
    public const XML_PATH_EMAIL_PRICE_TEMPLATE = 'catalog/productalert/email_price_template';

    public const XML_PATH_EMAIL_STOCK_TEMPLATE = 'catalog/productalert/email_stock_template';

    public const XML_PATH_EMAIL_IDENTITY       = 'catalog/productalert/email_identity';

    /**
     * Type
     *
     * @var string
     */
    protected $_type = 'price';

    /**
     * Website Model
     *
     * @var null|Mage_Core_Model_Website
     */
    protected $_website;

    /**
     * Customer model
     *
     * @var null|Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Products collection where changed price
     *
     * @var array
     */
    protected $_priceProducts = [];

    /**
     * Product collection which of back in stock
     *
     * @var array
     */
    protected $_stockProducts = [];

    /**
     * Price block
     *
     * @var null|Mage_ProductAlert_Block_Email_Price
     */
    protected $_priceBlock;

    /**
     * Stock block
     *
     * @var null|Mage_ProductAlert_Block_Email_Stock
     */
    protected $_stockBlock;

    /**
     * Set model type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * Retrieve model type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set website model
     *
     * @return $this
     */
    public function setWebsite(Mage_Core_Model_Website $website)
    {
        $this->_website = $website;
        return $this;
    }

    /**
     * Set website id
     *
     * @param  int   $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        $this->_website = Mage::app()->getWebsite($websiteId);
        return $this;
    }

    /**
     * Set customer by id
     *
     * @param  int   $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        $this->_customer = Mage::getModel('customer/customer')->load($customerId);
        return $this;
    }

    /**
     * Set customer model
     *
     * @return $this
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->_customer = $customer;
        return $this;
    }

    /**
     * Clean data
     *
     * @return $this
     */
    public function clean()
    {
        $this->_customer      = null;
        $this->_priceProducts = [];
        $this->_stockProducts = [];

        return $this;
    }

    /**
     * Add product (price change) to collection
     *
     * @return $this
     */
    public function addPriceProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_priceProducts[$product->getId()] = $product;
        return $this;
    }

    /**
     * Add product (back in stock) to collection
     *
     * @return $this
     */
    public function addStockProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_stockProducts[$product->getId()] = $product;
        return $this;
    }

    /**
     * Retrieve price block
     *
     * @return Mage_ProductAlert_Block_Email_Price
     */
    protected function _getPriceBlock()
    {
        if (is_null($this->_priceBlock)) {
            $this->_priceBlock = Mage::helper('productalert')
                ->createBlock('productalert/email_price');
        }

        return $this->_priceBlock;
    }

    /**
     * Retrieve stock block
     *
     * @return Mage_ProductAlert_Block_Email_Stock
     */
    protected function _getStockBlock()
    {
        if (is_null($this->_stockBlock)) {
            $this->_stockBlock = Mage::helper('productalert')
                ->createBlock('productalert/email_stock');
        }

        return $this->_stockBlock;
    }

    /**
     * Send customer email
     *
     * @return bool
     */
    public function send()
    {
        if (is_null($this->_website) || is_null($this->_customer)) {
            return false;
        }

        if (($this->_type == 'price' && count($this->_priceProducts) == 0)
            || ($this->_type == 'stock' && count($this->_stockProducts) == 0)
        ) {
            return false;
        }

        if (!$this->_website->getDefaultGroup() || !$this->_website->getDefaultGroup()->getDefaultStore()) {
            return false;
        }

        $store      = Mage::getModel('core/store')->load($this->_customer->getStoreId());
        $storeId    = $store->getId();
        if ($this->_type == 'price' && !Mage::getStoreConfig(self::XML_PATH_EMAIL_PRICE_TEMPLATE, $storeId)) {
            return false;
        }

        if ($this->_type == 'stock' && !Mage::getStoreConfig(self::XML_PATH_EMAIL_STOCK_TEMPLATE, $storeId)) {
            return false;
        }

        if ($this->_type != 'price' && $this->_type != 'stock') {
            return false;
        }

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        if ($this->_type == 'price') {
            $this->_getPriceBlock()
                ->setStore($store)
                ->reset();
            foreach ($this->_priceProducts as $product) {
                $product->setCustomerGroupId($this->_customer->getGroupId());
                $this->_getPriceBlock()->addProduct($product);
            }

            $block = $this->_getPriceBlock()->toHtml();
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_PRICE_TEMPLATE, $storeId);
        } else {
            $this->_getStockBlock()
                ->setStore($store)
                ->reset();
            foreach ($this->_stockProducts as $product) {
                $product->setCustomerGroupId($this->_customer->getGroupId());
                $this->_getStockBlock()->addProduct($product);
            }

            $block = $this->_getStockBlock()->toHtml();
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_STOCK_TEMPLATE, $storeId);
        }

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        Mage::getModel('core/email_template')
            ->setDesignConfig([
                'area'  => 'frontend',
                'store' => $storeId,
            ])->sendTransactional(
                $templateId,
                Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId),
                $this->_customer->getEmail(),
                $this->_customer->getName(),
                [
                    'customerName'  => $this->_customer->getName(),
                    'alertGrid'     => $block,
                ],
            );

        return true;
    }
}
