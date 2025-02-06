<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
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
