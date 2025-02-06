<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Catalog category landing page attribute source
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Category_Attribute_Source_Mode extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Returns all mode options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                [
                    'value' => Mage_Catalog_Model_Category::DM_PRODUCT,
                    'label' => Mage::helper('catalog')->__('Products only'),
                ],
                [
                    'value' => Mage_Catalog_Model_Category::DM_PAGE,
                    'label' => Mage::helper('catalog')->__('Static block only'),
                ],
                [
                    'value' => Mage_Catalog_Model_Category::DM_MIXED,
                    'label' => Mage::helper('catalog')->__('Static block and products'),
                ],
            ];
        }
        return $this->_options;
    }
}
