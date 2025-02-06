<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Entity/Attribute/Model - select product design options container from config
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Entity_Product_Attribute_Design_Options_Container extends Mage_Eav_Model_Entity_Attribute_Source_Config
{
    protected $_configNodePath;

    public function __construct()
    {
        $this->_configNodePath = 'global/catalog/product/design/options_container';
    }

    /**
     * Get a text for option value
     *
     * @param string|int $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        if (count($options)) {
            foreach ($options as $option) {
                if (isset($option['value']) && $option['value'] == $value) {
                    return $option['label'];
                }
            }
        }
        return $options[$value] ?? false;
    }
}
