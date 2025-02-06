<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Catalog Category *_sort_by Attributes Source Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Category_Attribute_Source_Sortby extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrieve Catalog Config Singleton
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getCatalogConfig()
    {
        return Mage::getSingleton('catalog/config');
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = [[
                'label' => Mage::helper('catalog')->__('Best Value'),
                'value' => 'position',
            ]];
            foreach ($this->_getCatalogConfig()->getAttributesUsedForSortBy() as $attribute) {
                $this->_options[] = [
                    'label' => Mage::helper('catalog')->__($attribute['frontend_label']),
                    'value' => $attribute['attribute_code'],
                ];
            }
        }
        return $this->_options;
    }
}
