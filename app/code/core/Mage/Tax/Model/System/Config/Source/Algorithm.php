<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * @package    Mage_Tax
 */
class Mage_Tax_Model_System_Config_Source_Algorithm
{
    protected $_options;

    public function __construct()
    {
        $this->_options = [
            [
                'value' => Mage_Tax_Model_Calculation::CALC_UNIT_BASE,
                'label' => Mage::helper('tax')->__('Unit Price'),
            ],
            [
                'value' => Mage_Tax_Model_Calculation::CALC_ROW_BASE,
                'label' => Mage::helper('tax')->__('Row Total'),
            ],
            [
                'value' => Mage_Tax_Model_Calculation::CALC_TOTAL_BASE,
                'label' => Mage::helper('tax')->__('Total'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_options;
    }
}
