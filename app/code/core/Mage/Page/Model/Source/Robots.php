<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        return array(
            Mage_Cms_Model_Page::META_ROBOTS_INDEX_FOLLOW =>
                array(
                    'value' => Mage_Cms_Model_Page::META_ROBOTS_INDEX_FOLLOW,
                    'label' => Mage::helper('cms')->__('INDEX,FOLLOW')
                ),
            Mage_Cms_Model_Page::META_ROBOTS_INDEX_NOFOLLOW =>
                array(
                    'value' => Mage_Cms_Model_Page::META_ROBOTS_INDEX_NOFOLLOW,
                    'label' => Mage::helper('cms')->__('INDEX,NOFOLLOW')
                ),
            Mage_Cms_Model_Page::META_ROBOTS_NOINDEX_FOLLOW =>
                array(
                    'value' => Mage_Cms_Model_Page::META_ROBOTS_NOINDEX_FOLLOW,
                    'label' => Mage::helper('cms')->__('NOINDEX,FOLLOW')
                ),
            Mage_Cms_Model_Page::META_ROBOTS_NOINDEX_NOFOLLOW =>
                array(
                    'value' => Mage_Cms_Model_Page::META_ROBOTS_NOINDEX_NOFOLLOW,
                    'label' => Mage::helper('cms')->__('NOINDEX,NOFOLLOW')
                ),
            Mage_Cms_Model_Page::META_ROBOTS_INDEX_FOLLOW_NOARCHIVE =>
                array(
                    'value' => Mage_Cms_Model_Page::META_ROBOTS_INDEX_FOLLOW_NOARCHIVE,
                    'label' => Mage::helper('cms')->__('INDEX,FOLLOW,NOARCHIVE')
                ),
            Mage_Cms_Model_Page::META_ROBOTS_INDEX_NOFOLLOW_NOARCHIVE =>
                array(
                    'value' => Mage_Cms_Model_Page::META_ROBOTS_INDEX_NOFOLLOW_NOARCHIVE,
                    'label' => Mage::helper('cms')->__('INDEX,NOFOLLOW,NOARCHIVE')
                ),
            Mage_Cms_Model_Page::META_ROBOTS_NOINDEX_NOFOLLOW_NOARCHIVE =>
                array(
                    'value' => Mage_Cms_Model_Page::META_ROBOTS_NOINDEX_NOFOLLOW_NOARCHIVE,
                    'label' => Mage::helper('cms')->__('NOINDEX,NOFOLLOW,NOARCHIVE')
                ),
        );
    }

    /**
     * @param int $key
     * @return string
     */
    public function getOptionLabel($key) {
        $options = $this->toOptionArray();

        return $options[$key]['label'];
    }
}