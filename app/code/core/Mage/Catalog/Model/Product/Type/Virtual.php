<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Virtual product type implementation
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Type_Virtual extends Mage_Catalog_Model_Product_Type_Abstract
{
    /**
     * Check is virtual product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isVirtual($product = null)
    {
        return true;
    }
}
