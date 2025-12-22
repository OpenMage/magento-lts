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
class Mage_Wishlist_Block_Customer_Wishlist_Items extends Mage_Core_Block_Template
{
    /**
     * Retrieve table column object list
     *
     * @return array
     */
    public function getColumns()
    {
        $columns = [];
        foreach ($this->getSortedChildren() as $code) {
            /** @var Mage_Wishlist_Block_Customer_Wishlist_Item_Column $child */
            $child = $this->getChild($code);
            if ($child->isEnabled()) {
                $columns[] = $child;
            }
        }

        return $columns;
    }
}
