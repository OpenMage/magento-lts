<?php
/**
 * Catalog search types
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
