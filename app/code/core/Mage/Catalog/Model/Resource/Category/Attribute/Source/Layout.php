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
