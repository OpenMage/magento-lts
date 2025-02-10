<?php
/**
 * Backend model for product url_key attribute
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
