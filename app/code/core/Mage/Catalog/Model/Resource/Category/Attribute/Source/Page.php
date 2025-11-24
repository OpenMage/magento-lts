<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog category landing page attribute source
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Category_Attribute_Source_Page extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Return all block options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('cms/block_collection')
                ->load()
                ->toOptionArray();
            array_unshift($this->_options, ['value' => '', 'label' => Mage::helper('catalog')->__('Please select a static block ...')]);
        }

        return $this->_options;
    }
}
