<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ConfigurableSwatches
 */

/**
 * @package    Mage_ConfigurableSwatches
 */
class Mage_ConfigurableSwatches_Model_System_Config_Source_Catalog_Product_Configattribute_Select extends Mage_ConfigurableSwatches_Model_System_Config_Source_Catalog_Product_Configattribute
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        if (is_null($this->_attributes)) {
            parent::toOptionArray();
            $attributes = $this->_attributes;

            array_unshift(
                $attributes,
                [
                    'value' => '',
                    'label' => Mage::helper('configurableswatches')->__('-- Please Select --'),
                ],
            );

            $this->_attributes = $attributes;
        }

        return $this->_attributes;
    }
}
