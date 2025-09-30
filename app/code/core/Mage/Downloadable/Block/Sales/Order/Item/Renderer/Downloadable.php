<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable order item render block
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Block_Sales_Order_Item_Renderer_Downloadable extends Mage_Sales_Block_Order_Item_Renderer_Default
{
    /**
     * @var Mage_Downloadable_Model_Link_Purchased
     */
    protected $_purchasedLinks = null;

    /**
     * @return Mage_Downloadable_Model_Link_Purchased
     */
    public function getLinks()
    {
        $this->_purchasedLinks = Mage::getModel('downloadable/link_purchased')
            ->load($this->getOrderItem()->getOrder()->getId(), 'order_id');
        $purchasedItems = Mage::getModel('downloadable/link_purchased_item')->getCollection()
            ->addFieldToFilter('order_item_id', $this->getOrderItem()->getId());
        $this->_purchasedLinks->setPurchasedItems($purchasedItems);

        return $this->_purchasedLinks;
    }

    /**
     * @return string
     */
    public function getLinksTitle()
    {
        if ($this->_purchasedLinks->getLinkSectionTitle()) {
            return $this->_purchasedLinks->getLinkSectionTitle();
        }
        return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }
}
