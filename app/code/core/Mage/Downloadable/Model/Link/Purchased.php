<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable links purchased model
 *
 * @package    Mage_Downloadable
 *
 * @method Mage_Downloadable_Model_Resource_Link_Purchased _getResource()
 * @method Mage_Downloadable_Model_Resource_Link_Purchased_Collection getCollection()
 * @method string getCreatedAt()
 * @method int getCustomerId()
 * @method string getLinkSectionTitle()
 * @method int getOrderId()
 * @method string getOrderIncrementId()
 * @method int getOrderItemId()
 * @method string getProductName()
 * @method string getProductSku()
 * @method Mage_Downloadable_Model_Resource_Link_Purchased getResource()
 * @method Mage_Downloadable_Model_Resource_Link_Purchased_Collection getResourceCollection()
 * @method string getUpdatedAt()
 * @method $this setCreatedAt(string $value)
 * @method $this setCustomerId(int $value)
 * @method $this setLinkSectionTitle(string $value)
 * @method $this setOrderId(int $value)
 * @method $this setOrderIncrementId(string $value)
 * @method $this setOrderItemId(int $value)
 * @method $this setProductName(string $value)
 * @method $this setProductSku(string $value)
 * @method $this setPurchasedItems(Mage_Downloadable_Model_Resource_Link_Purchased_Item_Collection $value)
 * @method $this setUpdatedAt(string $value)
 */
class Mage_Downloadable_Model_Link_Purchased extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('downloadable/link_purchased');
        parent::_construct();
    }

    /**
     * Check order id
     *
     * @inheritDoc
     */
    public function _beforeSave()
    {
        if ($this->getOrderId() == null) {
            throw new Exception(
                Mage::helper('downloadable')->__('Order id cannot be null'),
            );
        }

        return parent::_beforeSave();
    }
}
