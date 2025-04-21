<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Backend model for product url_key attribute
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Product_Attribute_Backend_Urlkey extends Mage_Catalog_Model_Product_Attribute_Backend_Urlkey
{
    /**
     * No need to validate url_key during import
     *
     * @param Mage_Catalog_Model_Product $object
     * @return $this
     */
    protected function _validateUrlKey($object)
    {
        return $this;
    }
}
