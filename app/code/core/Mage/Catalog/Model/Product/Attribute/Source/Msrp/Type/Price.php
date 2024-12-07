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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Source model for 'msrp_display_actual_price_type' product attribute
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Price extends Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type
{
    /**
     * Get value from the store configuration settings
     */
    public const TYPE_USE_CONFIG = '4';

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = parent::getAllOptions();
            $this->_options[] = [
                'label' => Mage::helper('catalog')->__('Use config'),
                'value' => self::TYPE_USE_CONFIG
            ];
        }
        return $this->_options;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        $attributeType = $this->getAttribute()->getBackendType();
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = [
            'unsigned'  => false,
            'default'   => null,
            'extra'     => null
        ];

        if (Mage::helper('core')->useDbCompatibleMode()) {
            $column['type']     = $attributeType;
            $column['is_null']  = true;
        } else {
            /** @var Mage_Eav_Model_Resource_Helper_Mysql4 $helper */
            $helper = Mage::getResourceHelper('eav');
            $column['type']     = $helper->getDdlTypeByColumnType($attributeType);
            $column['nullable'] = true;
        }

        return [$attributeCode => $column];
    }

    /**
     * Retrieve select for flat attribute update
     *
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
