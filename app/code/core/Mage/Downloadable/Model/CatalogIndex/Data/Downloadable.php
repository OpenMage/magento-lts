<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */

/**
 * Downloadable product data retriever
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_CatalogIndex_Data_Downloadable extends Mage_CatalogIndex_Model_Data_Virtual
{
    /**
     * Retrieve product type code
     *
     * @return string
     */
    public function getTypeCode()
    {
        return Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE;
    }
}
