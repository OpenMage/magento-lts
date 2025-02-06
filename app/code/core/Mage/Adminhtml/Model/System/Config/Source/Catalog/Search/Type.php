<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Catalog search types
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Catalog_Search_Type
{
    public function toOptionArray()
    {
        $types = [
            Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_LIKE     => 'Like',
            Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_FULLTEXT => 'Fulltext',
            Mage_CatalogSearch_Model_Fulltext::SEARCH_TYPE_COMBINE  => 'Combine (Like and Fulltext)',
        ];
        $options = [];
        foreach ($types as $k => $v) {
            $options[] = [
                'value' => $k,
                'label' => $v,
            ];
        }
        return $options;
    }
}
