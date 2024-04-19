<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Page
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Page robots source
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Model_Source_Robots
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            Mage_Cms_Model_Page::META_ROBOTS_INDEX_FOLLOW => [
                'value' => Mage_Cms_Model_Page::META_ROBOTS_INDEX_FOLLOW,
                'label' => Mage::helper('cms')->__('INDEX,FOLLOW')
            ],
            Mage_Cms_Model_Page::META_ROBOTS_INDEX_NOFOLLOW => [
                'value' => Mage_Cms_Model_Page::META_ROBOTS_INDEX_NOFOLLOW,
                'label' => Mage::helper('cms')->__('INDEX,NOFOLLOW')
            ],
            Mage_Cms_Model_Page::META_ROBOTS_NOINDEX_FOLLOW => [
                'value' => Mage_Cms_Model_Page::META_ROBOTS_NOINDEX_FOLLOW,
                'label' => Mage::helper('cms')->__('NOINDEX,FOLLOW')
            ],
            Mage_Cms_Model_Page::META_ROBOTS_NOINDEX_NOFOLLOW => [
                'value' => Mage_Cms_Model_Page::META_ROBOTS_NOINDEX_NOFOLLOW,
                'label' => Mage::helper('cms')->__('NOINDEX,NOFOLLOW')
            ],
            Mage_Cms_Model_Page::META_ROBOTS_INDEX_FOLLOW_NOARCHIVE => [
                'value' => Mage_Cms_Model_Page::META_ROBOTS_INDEX_FOLLOW_NOARCHIVE,
                'label' => Mage::helper('cms')->__('INDEX,FOLLOW,NOARCHIVE')
            ],
            Mage_Cms_Model_Page::META_ROBOTS_INDEX_NOFOLLOW_NOARCHIVE => [
                'value' => Mage_Cms_Model_Page::META_ROBOTS_INDEX_NOFOLLOW_NOARCHIVE,
                'label' => Mage::helper('cms')->__('INDEX,NOFOLLOW,NOARCHIVE')
            ],
            Mage_Cms_Model_Page::META_ROBOTS_NOINDEX_NOFOLLOW_NOARCHIVE => [
                'value' => Mage_Cms_Model_Page::META_ROBOTS_NOINDEX_NOFOLLOW_NOARCHIVE,
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
        $options = $this->toOptionArray();
        return $options[$key]['label'];
    }
}
