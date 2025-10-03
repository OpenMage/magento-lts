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
class Mage_Catalog_Model_Resource_Category_Attribute_Source_Layout extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Return cms layout update options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $layouts = [];
            foreach (Mage::getConfig()->getNode('global/cms/layouts')->children() as $layoutName => $layoutConfig) {
                $this->_options[] = [
                    'value' => $layoutName,
                    'label' => (string) $layoutConfig->label,
                ];
            }
            array_unshift($this->_options, ['value' => '', 'label' => Mage::helper('catalog')->__('No layout updates')]);
        }
        return $this->_options;
    }
}
