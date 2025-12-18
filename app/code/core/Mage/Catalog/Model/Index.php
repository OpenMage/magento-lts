<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog Category/Product Index
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Index
{
    /**
     * Rebuild indexes
     *
     * @return $this
     * @throws Mage_Core_Exception
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
