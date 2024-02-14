<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
