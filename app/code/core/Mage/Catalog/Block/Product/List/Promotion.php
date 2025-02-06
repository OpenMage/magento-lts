<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_List_Promotion extends Mage_Catalog_Block_Product_List
{
    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection|Mage_Eav_Model_Entity_Collection_Abstract|Object
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $collection = Mage::getResourceModel('catalog/product_collection');
            Mage::getModel('catalog/layer')->prepareProductCollection($collection);
            // your custom filter
            $collection->addAttributeToFilter('promotion', 1)
                ->addStoreFilter();

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}
