<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Catalog grouped product info block
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Product_View_Type_Grouped extends Mage_Catalog_Block_Product_View_Abstract
{
    /**
     * @return array
     */
    public function getAssociatedProducts()
    {
        /** @var Mage_Catalog_Model_Product_Type_Grouped $productType */
        $productType = $this->getProduct()->getTypeInstance(true);
        return $productType->getAssociatedProducts($this->getProduct());
    }

    /**
     * Set preconfigured values to grouped associated products
     *
     * @return $this
     */
    public function setPreconfiguredValue()
    {
        $configValues = $this->getProduct()->getPreconfiguredValues()->getSuperGroup();
        if (is_array($configValues)) {
            $associatedProducts = $this->getAssociatedProducts();
            foreach ($associatedProducts as $item) {
                if (isset($configValues[$item->getId()])) {
                    $item->setQty($configValues[$item->getId()]);
                }
            }
        }
        return $this;
    }
}
