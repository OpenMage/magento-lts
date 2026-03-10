<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Product image attribute frontend
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Frontend_Image extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    /**
     * @param  Varien_Object                   $object
     * @param  string                          $size
     * @return false|string
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getUrl($object, $size = null)
    {
        $url = false;
        $image = $object->getData($this->getAttribute()->getAttributeCode());

        if (!is_null($size) && file_exists(Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS . $size . DS . $image)) {
            # resized image is cached
            $url = Mage::app()->getStore($object->getStore())->getBaseUrl('media') . 'catalog/product/' . $size . '/' . $image;
        } elseif (!is_null($size)) {
            # resized image is not cached
            $url = Mage::app()->getStore($object->getStore())->getBaseUrl() . 'catalog/product/image/size/' . $size . '/' . $image;
        } elseif ($image) {
            # using original image
            $url = Mage::app()->getStore($object->getStore())->getBaseUrl('media') . 'catalog/product/' . $image;
        }

        return $url;
    }
}
