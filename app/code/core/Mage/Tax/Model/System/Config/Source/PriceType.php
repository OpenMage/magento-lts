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
class Mage_Tax_Model_System_Config_Source_PriceType
{
    protected $_options;

    public function __construct()
    {
        $this->_options = [
            [
                'value' => 0,
                'label' => Mage::helper('tax')->__('Excluding Tax'),
            ],
            [
                'value' => 1,
                'label' => Mage::helper('tax')->__('Including Tax'),
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
