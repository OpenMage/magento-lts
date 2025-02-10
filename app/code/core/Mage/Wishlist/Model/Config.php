<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Model_Config
{
    public const XML_PATH_PRODUCT_ATTRIBUTES = 'global/wishlist/item/product_attributes';

    /**
     * Get product attributes that need in wishlist
     *
     */
    public function getProductAttributes()
    {
        $attrsForCatalog  = Mage::getSingleton('catalog/config')->getProductAttributes();
        $attrsForWishlist = Mage::getConfig()->getNode(self::XML_PATH_PRODUCT_ATTRIBUTES)->asArray();

        return array_merge($attrsForCatalog, array_keys($attrsForWishlist));
    }
}
