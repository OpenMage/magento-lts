<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/**
 * Wishlist block customer items
 *
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Block_Share_Email_Items extends Mage_Wishlist_Block_Abstract
{
    /**
     * Initialize template
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('wishlist/email/items.phtml');
    }

    /**
     * Retrieve Product View URL
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getProductUrl($product, $additional = [])
    {
        $additional['_store_to_url'] = true;
        return parent::getProductUrl($product, $additional);
    }

    /**
     * Retrieve URL for add product to shopping cart
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        return $this->getAddToCartUrlCustom($product, $additional);
    }

    /**
     * Check whether wishlist item has description
     *
     * @param Mage_Wishlist_Model_Item $item
     * @return bool
     */
    public function hasDescription($item)
    {
        $hasDescription = parent::hasDescription($item);
        if ($hasDescription) {
            return ($item->getDescription() !== Mage::helper('wishlist')->defaultCommentString());
        }

        return $hasDescription;
    }

    /**
     * Retrieve URL for add product to shopping cart with or without Form Key
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @param bool $addFormKey
     * @return string
     */
    public function getAddToCartUrlCustom($product, $additional = [], $addFormKey = true)
    {
        $additional['nocookie'] = 1;
        $additional['_store_to_url'] = true;
        return parent::getAddToCartUrlCustom($product, $additional, $addFormKey);
    }
}
