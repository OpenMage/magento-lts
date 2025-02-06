<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Catalog product media api V2
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Media_Api_V2 extends Mage_Catalog_Model_Product_Attribute_Media_Api
{
    /**
     * Prepare data to create or update image
     *
     * @param stdClass $data
     * @return array
     */
    protected function _prepareImageData($data)
    {
        if (!is_object($data)) {
            return parent::_prepareImageData($data);
        }
        $_imageData = get_object_vars($data);
        if (isset($data->file) && is_object($data->file)) {
            $_imageData['file'] = get_object_vars($data->file);
        }
        return parent::_prepareImageData($_imageData);
    }
}
