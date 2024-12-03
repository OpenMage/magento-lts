<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
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
                'label' => Mage::helper('tax')->__('Unit Price')
            ],
            [
                'value' => Mage_Tax_Model_Calculation::CALC_ROW_BASE,
                'label' => Mage::helper('tax')->__('Row Total')
            ],
            [
                'value' => Mage_Tax_Model_Calculation::CALC_TOTAL_BASE,
                'label' => Mage::helper('tax')->__('Total')
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
