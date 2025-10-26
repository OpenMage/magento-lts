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
class Mage_Catalog_Model_Entity_Product_Attribute_Frontend_Image extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    /**
     * @param Varien_Object $object
     * @param string $size
     * @return bool|string
     */
    public function getUrl($object, $size = null)
    {
        $url = false;
        $image = $object->getData($this->getAttribute()->getAttributeCode());

        if (!is_null($size) && file_exists(Mage::getBaseDir('media') . '/catalog/product/' . $size . '/' . $image)) {
            // image is cached
            $url = Mage::getBaseUrl('media') . 'catalog/product/' . $size . '/' . $image;
        } elseif (!is_null($size)) {
            // image is not cached
            $url = Mage::getBaseUrl() . 'catalog/product/image/size/' . $size . '/' . $image;
        } else {
            // image is not cached
            $url = Mage::getBaseUrl() . 'catalog/product/image' . $image;
        }

        return $url;
    }
}
