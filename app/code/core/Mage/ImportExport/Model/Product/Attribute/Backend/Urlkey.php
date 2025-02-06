<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_ImportExport
 */

/**
 * Backend model for product url_key attribute
 *
 * @category   Mage
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
