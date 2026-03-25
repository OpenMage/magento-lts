<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Category image attribute frontend
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Category_Attribute_Frontend_Image extends Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
{
    public const IMAGE_PATH_SEGMENT = 'catalog/category/';

    /**
     * @param  Varien_Object $object
     * @return null|string
     */
    public function getUrl($object)
    {
        if ($image = $object->getData($this->getAttribute()->getAttributeCode())) {
            return Mage::getBaseUrl('media') . self::IMAGE_PATH_SEGMENT . $image;
        }

        return null ;
    }
}
