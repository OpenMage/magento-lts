<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block to display downloadable links bought by customer
 *
 * @category   Mage
 * @package    Mage_Downloadable
 *
 * @method Mage_Downloadable_Model_Resource_Link_Purchased_Item_Collection getItems()
 * @method $this setItems(Mage_Downloadable_Model_Resource_Link_Purchased_Item_Collection $value)
 * @method Mage_Downloadable_Model_Resource_Link_Purchased_Collection getPurchased()
 * @method $this setPurchased(Mage_Downloadable_Model_Resource_Link_Purchased_Collection $value)
 * @method string getRefererUrl()
 * @method $this setRefererUrl(string $value)
 */
class Mage_Downloadable_Block_Customer_Products_List extends Mage_Core_Block_Template
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $session = Mage::getSingleton('customer/session');
        $purchased = Mage::getResourceModel('downloadable/link_purchased_collection')
            ->addFieldToFilter('customer_id', $session->getCustomerId())
            ->addOrder('created_at', 'desc');
        $this->setPurchased($purchased);
        $purchasedIds = [];
        /** @var Mage_Downloadable_Model_Link_Purchased_Item $item */
        foreach ($purchased as $item) {
            $purchasedIds[] = $item->getId();
        }
        if (empty($purchasedIds)) {
            $purchasedIds = [null];
        }
        $purchasedItems = Mage::getResourceModel('downloadable/link_purchased_item_collection')
            ->addFieldToFilter('purchased_id', ['in' => $purchasedIds])
            ->addFieldToFilter(
                'status',
                [
                    'nin' => [
                        Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT,
                        Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PAYMENT_REVIEW,
                    ],
                ],
            )
            ->setOrder('item_id', 'desc');
        $this->setItems($purchasedItems);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'downloadable.customer.products.pager')
            ->setCollection($this->getItems());
        $this->setChild('pager', $pager);
        $this->getItems()->load();
        /** @var Mage_Downloadable_Model_Link_Purchased_Item $item */
        foreach ($this->getItems() as $item) {
            $item->setPurchased($this->getPurchased()->getItemById($item->getPurchasedId()));
        }
        return $this;
    }

    /**
     * Return order view url
     *
     * @param int $orderId
     * @return string
     */
    public function getOrderViewUrl($orderId)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }

    /**
     * Return number of left downloads or unlimited
     *
     * @param Mage_Downloadable_Model_Link_Purchased_Item $item
     * @return int|string
     */
    public function getRemainingDownloads($item)
    {
        if ($item->getNumberOfDownloadsBought()) {
            return $item->getNumberOfDownloadsBought() - $item->getNumberOfDownloadsUsed();
        }
        return Mage::helper('downloadable')->__('Unlimited');
    }

    /**
     * Return url to download link
     *
     * @param Mage_Downloadable_Model_Link_Purchased_Item $item
     * @return string
     */
    public function getDownloadUrl($item)
    {
        return $this->getUrl('*/download/link', ['id' => $item->getLinkHash(), '_secure' => true]);
    }

    /**
     * Return true if target of link new window
     *
     * @return bool
     */
    public function getIsOpenInNewWindow()
    {
        return Mage::getStoreConfigFlag(Mage_Downloadable_Model_Link::XML_PATH_TARGET_NEW_WINDOW);
    }
}
