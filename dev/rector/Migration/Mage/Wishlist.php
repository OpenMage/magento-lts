<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Mage;

use Mage_Wishlist_Block_Abstract;
use Mage_Wishlist_Block_Customer_Sidebar;
use Mage_Wishlist_Helper_Data;
use Rector\Renaming\ValueObject\MethodCallRename;

final class Wishlist
{
    /**
     * @return MethodCallRename[]
     */
    public static function renameMethod(): array
    {
        return [
            new MethodCallRename(Mage_Wishlist_Block_Abstract::class, 'getWishlist', 'getWishlistItems'),
            new MethodCallRename(Mage_Wishlist_Block_Customer_Sidebar::class, 'getRemoveItemUrl', 'getItemRemoveUrl'),
            new MethodCallRename(Mage_Wishlist_Block_Customer_Sidebar::class, 'getAddToCartItemUrl', 'getItemAddToCartUrl'),
            new MethodCallRename(Mage_Wishlist_Helper_Data::class, 'getItemCollection', 'getProductCollection'),
        ];
    }
}
