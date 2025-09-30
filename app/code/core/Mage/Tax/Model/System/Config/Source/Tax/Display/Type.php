<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * Price display type source model
 *
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
