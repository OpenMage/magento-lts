<?php
/**
 * Date retriever abstract model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Data_Simple extends Mage_CatalogIndex_Model_Data_Abstract
{
    protected $_haveChildren = false;

    /**
     * Retrieve product type code
     * @return string
     */
    public function getTypeCode()
    {
        return Mage_Catalog_Model_Product_Type::TYPE_SIMPLE;
    }
}
