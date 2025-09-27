<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Product attribute source model for enable/disable option
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Source_Boolean extends Mage_Eav_Model_Entity_Attribute_Source_Boolean
{
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
                    'value' => 1,
                ],
                [
                    'label' => Mage::helper('catalog')->__('No'),
                    'value' => 0,
                ],
                [
                    'label' => Mage::helper('catalog')->__('Use config'),
                    'value' => 2,
                ],
            ];
        }
        return $this->_options;
    }
}
