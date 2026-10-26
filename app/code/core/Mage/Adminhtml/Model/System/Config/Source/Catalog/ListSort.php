<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml Catalog Product List Sortable allowed sortable attributes source
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Catalog_ListSort
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
            'label' => Mage::helper('catalog')->__('Best Value'),
            'value' => 'position',
        ];
        foreach ($this->_getCatalogConfig()->getAttributesUsedForSortBy() as $attribute) {
            $options[] = [
                'label' => Mage::helper('catalog')->__($attribute['frontend_label']),
                'value' => $attribute['attribute_code'],
            ];
        }

        return $options;
    }

    /**
     * Retrieve Catalog Config Singleton
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getCatalogConfig()
    {
        return Mage::getSingleton('catalog/config');
    }
}
