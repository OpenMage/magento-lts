<?php
/**
 * Downloadable product data retriever
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
