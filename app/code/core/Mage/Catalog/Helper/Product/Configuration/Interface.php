<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Interface for product configuration helpers
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
interface Mage_Catalog_Helper_Product_Configuration_Interface
{
    /**
     * Retrieves product options list
     *
     * @return array
     */
    public function getOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item);
}
