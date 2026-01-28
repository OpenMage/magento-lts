<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable links purchased item model
 *
 * @package    Mage_Downloadable
 *
 * @method Mage_Downloadable_Model_Resource_Link_Purchased_Item            _getResource()
 * @method Mage_Downloadable_Model_Resource_Link_Purchased_Item_Collection getCollection()
 * @method int                                                             getIsShareable()
 * @method string                                                          getLinkFile()
 * @method string                                                          getLinkHash()
 * @method int                                                             getLinkId()
 * @method string                                                          getLinkTitle()
 * @method string                                                          getLinkType()
 * @method string                                                          getLinkUrl()
 * @method int                                                             getNumberOfDownloadsBought()
 * @method int                                                             getNumberOfDownloadsUsed()
 * @method Mage_Sales_Model_Order                                          getOrder()
 * @method int                                                             getOrderItemId()
 * @method int                                                             getProductId()
 * @method int                                                             getPurchasedId()
 * @method Mage_Downloadable_Model_Resource_Link_Purchased_Item            getResource()
 * @method Mage_Downloadable_Model_Resource_Link_Purchased_Item_Collection getResourceCollection()
 * @method string                                                          getStatus()
 * @method $this                                                           setIsShareable(int $value)
 * @method $this                                                           setLinkFile(string $value)
 * @method $this                                                           setLinkHash(string $value)
 * @method $this                                                           setLinkId(int $value)
 * @method $this                                                           setLinkTitle(string $value)
 * @method $this                                                           setLinkType(string $value)
 * @method $this                                                           setLinkUrl(string $value)
 * @method $this                                                           setNumberOfDownloadsBought(int $value)
 * @method $this                                                           setNumberOfDownloadsUsed(int $value)
 * @method $this                                                           setOrderItemId(int $value)
 * @method $this                                                           setProductId(int $value)
 * @method $this                                                           setPurchasedId(int $value)
 * @method $this                                                           setStatus(string $value)
 */
class Mage_Downloadable_Model_Link_Purchased_Item extends Mage_Core_Model_Abstract
{
    public const XML_PATH_ORDER_ITEM_STATUS = 'catalog/downloadable/order_item_status';

    public const LINK_STATUS_PENDING   = 'pending';

    public const LINK_STATUS_AVAILABLE = 'available';

    public const LINK_STATUS_EXPIRED   = 'expired';

    public const LINK_STATUS_PENDING_PAYMENT = 'pending_payment';

    public const LINK_STATUS_PAYMENT_REVIEW = 'payment_review';

    /**
     * @inheritDoc
     */
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
    protected function _beforeSave()
    {
        if ($this->getOrderItemId() == null) {
            throw new Exception(
                Mage::helper('downloadable')->__('Order item id cannot be null'),
            );
        }

        return parent::_beforeSave();
    }
}
