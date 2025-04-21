<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/**
 * Item option model
 *
 * @package    Mage_Wishlist
 *
 * @method Mage_Wishlist_Model_Resource_Item_Option_Collection getCollection()
 * @method string getCode()
 * @method int getProductId()
 * @method $this setProductId(int $value)
 * @method int getWishlistItemId()
 * @method $this setWishlistItemId(int $value)
 * @method $this setValue(string $sBuyRequest)
 */
class Mage_Wishlist_Model_Item_Option extends Mage_Core_Model_Abstract implements Mage_Catalog_Model_Product_Configuration_Item_Option_Interface
{
    protected $_item;
    protected $_product;

    protected function _construct()
    {
        $this->_init('wishlist/item_option');
    }

    /**
     * Checks that item option model has data changes
     *
     * @return bool
     */
    protected function _hasModelChanged()
    {
        if (!$this->hasDataChanges()) {
            return false;
        }

        return $this->_getResource()->hasDataChanged($this);
    }

    /**
     * Set quote item
     *
     * @param   Mage_Wishlist_Model_Item $item
     * @return  Mage_Wishlist_Model_Item_Option
     */
    public function setItem($item)
    {
        $this->_item = $item;
        if ($this->getWishlistItemId() != $item->getId()) {
            $this->setWishlistItemId($item->getId());
        }
        return $this;
    }

    /**
     * Get option item
     *
     * @return Mage_Wishlist_Model_Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Set option product
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Wishlist_Model_Item_Option
     */
    public function setProduct($product)
    {
        $this->_product = $product;
        if ($this->getProductId() != $product->getId()) {
            $this->setProductId($product->getId());
        }
        return $this;
    }

    /**
     * Get option product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Get option value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->_getData('value');
    }

    /**
     * Initialize item identifier before save data
     *
     * @inheritDoc
     */
    protected function _beforeSave()
    {
        if ($this->getItem()) {
            $this->setWishlistItemId($this->getItem()->getId());
        }
        return parent::_beforeSave();
    }

    /**
     * Clone option object
     */
    public function __clone()
    {
        $this->setId(null);
        $this->_item    = null;
    }
}
