<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Catalog Category/Product Index
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Index
{
    /**
     * Rebuild indexes
     * @return $this
     */
    public function rebuild()
    {
        Mage::getResourceSingleton('catalog/category')
            ->refreshProductIndex();
        foreach (Mage::app()->getStores() as $store) {
            Mage::getResourceSingleton('catalog/product')
                ->refreshEnabledIndex($store);
        }
        return $this;
    }
}
