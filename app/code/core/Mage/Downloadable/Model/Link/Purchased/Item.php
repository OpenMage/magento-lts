<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable links purchased item model
 *
 * @method Mage_Downloadable_Model_Resource_Link_Purchased_Item _getResource()
 * @method Mage_Downloadable_Model_Resource_Link_Purchased_Item getResource()
 * @method Mage_Downloadable_Model_Resource_Link_Purchased_Item_Collection getCollection()
 *
 * @method int getPurchasedId()
 * @method $this setPurchasedId(int $value)
 * @method int getOrderItemId()
 * @method $this setOrderItemId(int $value)
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method string getLinkHash()
 * @method $this setLinkHash(string $value)
 * @method int getNumberOfDownloadsBought()
 * @method $this setNumberOfDownloadsBought(int $value)
 * @method int getNumberOfDownloadsUsed()
 * @method $this setNumberOfDownloadsUsed(int $value)
 * @method int getLinkId()
 * @method $this setLinkId(int $value)
 * @method string getLinkTitle()
 * @method $this setLinkTitle(string $value)
 * @method int getIsShareable()
 * @method $this setIsShareable(int $value)
 * @method string getLinkUrl()
 * @method $this setLinkUrl(string $value)
 * @method string getLinkFile()
 * @method $this setLinkFile(string $value)
 * @method string getLinkType()
 * @method $this setLinkType(string $value)
 * @method string getStatus()
 * @method $this setStatus(string $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $value)
 * @method Mage_Sales_Model_Order getOrder()
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Link_Purchased_Item extends Mage_Core_Model_Abstract
{
    const XML_PATH_ORDER_ITEM_STATUS = 'catalog/downloadable/order_item_status';

    const LINK_STATUS_PENDING   = 'pending';
    const LINK_STATUS_AVAILABLE = 'available';
    const LINK_STATUS_EXPIRED   = 'expired';
    const LINK_STATUS_PENDING_PAYMENT = 'pending_payment';
    const LINK_STATUS_PAYMENT_REVIEW = 'payment_review';

    protected function _construct()
    {
        $this->_init('downloadable/link_purchased_item');
        parent::_construct();
    }

    /**
     * Check order item id
     *
     * @return Mage_Core_Model_Abstract
     */
    public function _beforeSave()
    {
        if ($this->getOrderItemId() == null) {
            throw new Exception(
                Mage::helper('downloadable')->__('Order item id cannot be null')
            );
        }
        return parent::_beforeSave();
    }
}
