<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/**
 * Price display type source model
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_System_Config_Source_Tax_Display_Type
{
    protected $_options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = [];
            $this->_options[] = ['value' => Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX, 'label' => Mage::helper('tax')->__('Excluding Tax')];
            $this->_options[] = ['value' => Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX, 'label' => Mage::helper('tax')->__('Including Tax')];
            $this->_options[] = ['value' => Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH, 'label' => Mage::helper('tax')->__('Including and Excluding Tax')];
        }
        return $this->_options;
    }
}
