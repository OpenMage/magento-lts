<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog category robots attribute source
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Category_Attribute_Source_Robots extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @return array
     */
    public function getAllOptions()
    {
        return [
            Mage_Catalog_Model_Category::META_ROBOTS_INDEX_FOLLOW => [
                'value' => Mage_Catalog_Model_Category::META_ROBOTS_INDEX_FOLLOW,
                'label' => Mage::helper('cms')->__('INDEX,FOLLOW')
            ],
            Mage_Catalog_Model_Category::META_ROBOTS_INDEX_NOFOLLOW => [
                'value' => Mage_Catalog_Model_Category::META_ROBOTS_INDEX_NOFOLLOW,
                'label' => Mage::helper('cms')->__('INDEX,NOFOLLOW')
            ],
            Mage_Catalog_Model_Category::META_ROBOTS_NOINDEX_FOLLOW => [
                'value' => Mage_Catalog_Model_Category::META_ROBOTS_NOINDEX_FOLLOW,
                'label' => Mage::helper('cms')->__('NOINDEX,FOLLOW')
            ],
            Mage_Catalog_Model_Category::META_ROBOTS_NOINDEX_NOFOLLOW => [
                'value' => Mage_Catalog_Model_Category::META_ROBOTS_NOINDEX_NOFOLLOW,
                'label' => Mage::helper('cms')->__('NOINDEX,NOFOLLOW')
            ],
            Mage_Catalog_Model_Category::META_ROBOTS_INDEX_FOLLOW_NOARCHIVE => [
                'value' => Mage_Catalog_Model_Category::META_ROBOTS_INDEX_FOLLOW_NOARCHIVE,
                'label' => Mage::helper('cms')->__('INDEX,FOLLOW,NOARCHIVE')
            ],
            Mage_Catalog_Model_Category::META_ROBOTS_INDEX_NOFOLLOW_NOARCHIVE => [
                'value' => Mage_Catalog_Model_Category::META_ROBOTS_INDEX_NOFOLLOW_NOARCHIVE,
                'label' => Mage::helper('cms')->__('INDEX,NOFOLLOW,NOARCHIVE')
            ],
            Mage_Catalog_Model_Category::META_ROBOTS_NOINDEX_NOFOLLOW_NOARCHIVE => [
                'value' => Mage_Catalog_Model_Category::META_ROBOTS_NOINDEX_NOFOLLOW_NOARCHIVE,
                'label' => Mage::helper('cms')->__('NOINDEX,NOFOLLOW,NOARCHIVE')
            ]
        ];
    }

    /**
     * @param int $key
     * @return string
     */
    public function getOptionLabel($key)
    {
        $options = $this->getAllOptions();
        return $options[$key]['label'];
    }
}
