<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Import proxy product resource
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Import_Proxy_Product_Resource extends Mage_Catalog_Model_Resource_Product
{
    /**
     * Product to category table.
     *
     * @return string
     */
    public function getProductCategoryTable()
    {
        return $this->_productCategoryTable;
    }

    /**
     * Product to website table.
     *
     * @return string
     */
    public function getProductWebsiteTable()
    {
        return $this->_productWebsiteTable;
    }
}
