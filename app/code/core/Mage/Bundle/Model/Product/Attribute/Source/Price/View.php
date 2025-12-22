<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Bundle Price View Attribute Renderer
 *
 * @package    Mage_Bundle
 */
class Mage_Bundle_Model_Product_Attribute_Source_Price_View extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = [
                [
                    'label' => Mage::helper('bundle')->__('As Low as'),
                    'value' =>  1,
                ],
                [
                    'label' => Mage::helper('bundle')->__('Price Range'),
                    'value' =>  0,
                ],
            ];
        }

        return $this->_options;
    }

    /**
     * Get a text for option value
     *
     * @param  int|string   $value
     * @return false|string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }

        return false;
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
            $column['type']     = 'int';
            $column['is_null']  = true;
        } else {
            $column['type']     = Varien_Db_Ddl_Table::TYPE_INTEGER;
            $column['nullable'] = true;
            $column['comment']  = 'Bundle Price View ' . $attributeCode . ' column';
        }

        return [$attributeCode => $column];
    }

    /**
     * Retrieve Select for update Attribute value in flat table
     *
     * @param  int                   $store
     * @return null|Varien_Db_Select
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute_option')
            ->getFlatUpdateSelect($this->getAttribute(), $store, false);
    }
}
