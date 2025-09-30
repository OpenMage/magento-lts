<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * Virtual product data retriever
 *
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Data_Virtual extends Mage_CatalogIndex_Model_Data_Simple
{
    protected $_haveChildren = false;

    /**
     * Retrieve product type code
     *
     * @return string
     */
    public function getTypeCode()
    {
        return Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL;
    }
}
