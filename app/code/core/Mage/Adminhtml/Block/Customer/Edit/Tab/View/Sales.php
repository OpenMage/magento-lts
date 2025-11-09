<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml customer view wishlist block
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_View_Sales extends Mage_Adminhtml_Block_Template
{
    /**
     * Sales entity collection
     *
     * @var Mage_Sales_Model_Entity_Sale_Collection
     */
    protected $_collection;

    protected $_groupedCollection;

    protected $_websiteCounts;

    /**
     * Currency model
     *
     * @var Mage_Directory_Model_Currency
     */
    protected $_currency;

    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_view_sales_grid');
    }

    public function _beforeToHtml()
    {
        $this->_currency = Mage::getModel('directory/currency')
            ->load(Mage_Directory_Helper_Data::getConfigCurrencyBase());

        $this->_collection = Mage::getResourceModel('sales/sale_collection')
            ->setCustomerFilter(Mage::registry('current_customer'))
            ->setOrderStateFilter(Mage_Sales_Model_Order::STATE_CANCELED, true)
            ->load();

        $this->_groupedCollection = [];

        foreach ($this->_collection as $sale) {
            if (!is_null($sale->getStoreId())) {
                $store      = Mage::app()->getStore($sale->getStoreId());
                $websiteId  = $store->getWebsiteId();
                $groupId    = $store->getGroupId();
                $storeId    = $store->getId();

                $sale->setWebsiteId($store->getWebsiteId());
                $sale->setWebsiteName($store->getWebsite()->getName());
                $sale->setGroupId($store->getGroupId());
                $sale->setGroupName($store->getGroup()->getName());
            } else {
                $websiteId  = 0;
                $groupId    = 0;
                $storeId    = 0;

                $sale->setStoreName(Mage::helper('customer')->__('Deleted Stores'));
            }

            $this->_groupedCollection[$websiteId][$groupId][$storeId] = $sale;
            $this->_websiteCounts[$websiteId] = isset($this->_websiteCounts[$websiteId]) ? $this->_websiteCounts[$websiteId] + 1 : 1;
        }

        return parent::_beforeToHtml();
    }

    public function getWebsiteCount($websiteId)
    {
        return $this->_websiteCounts[$websiteId] ?? 0;
    }

    public function getRows()
    {
        return $this->_groupedCollection;
    }

    public function getTotals()
    {
        return $this->_collection->getTotals();
    }

    /**
     * @param float $price
     * @return string
     * @deprecated after 1.4.0.0-rc1
     */
    public function getPriceFormatted($price)
    {
        return $this->_currency->format($price);
    }

    /**
     * Format price by specified website
     *
     * @param float $price
     * @param null|int $websiteId
     * @return string
     */
    public function formatCurrency($price, $websiteId = null)
    {
        return Mage::app()->getWebsite($websiteId)->getBaseCurrency()->format($price);
    }
}
