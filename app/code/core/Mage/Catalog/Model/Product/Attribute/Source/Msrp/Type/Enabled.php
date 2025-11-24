<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Source model for 'msrp_enabled' product attribute
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Enable MAP
     */
    public const MSRP_ENABLE_YES = 1;

    /**
     * Disable MAP
     */
    public const MSRP_ENABLE_NO = 0;

    /**
     * Get value from the store configuration settings
     */
    public const MSRP_ENABLE_USE_CONFIG = 2;

    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                [
                    'label' => Mage::helper('catalog')->__('Yes'),
                    'value' => self::MSRP_ENABLE_YES,
                ],
                [
                    'label' => Mage::helper('catalog')->__('No'),
                    'value' => self::MSRP_ENABLE_NO,
                ],
                [
                    'label' => Mage::helper('catalog')->__('Use config'),
                    'value' => self::MSRP_ENABLE_USE_CONFIG,
                ],
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
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = [
            'unsigned'  => false,
            'default'   => null,
            'extra'     => null,
        ];

        if (Mage::helper('core')->useDbCompatibleMode()) {
            $column['type']     = 'tinyint(1)';
            $column['is_null']  = true;
        } else {
            $column['type']     = Varien_Db_Ddl_Table::TYPE_SMALLINT;
            $column['length']   = 1;
            $column['nullable'] = true;
            $column['comment']  = $attributeCode . ' column';
        }

        return [$attributeCode => $column];
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return null|Varien_Db_Select
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
