<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Wishlist
 */

/**
 * Wishlist block customer items
 *
 * @category   Mage
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
            $child = $this->getChild($code);
            if ($child->isEnabled()) {
                $columns[] = $child;
            }
        }
        return $columns;
    }
}
