<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Catalog product landing page attribute source
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Source_Layout extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getSingleton('page/source_layout')->toOptionArray();
            array_unshift($this->_options, ['value' => '', 'label' => Mage::helper('catalog')->__('No layout updates')]);
        }
        return $this->_options;
    }
}
