<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Weee
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Weee
 */
class Mage_Weee_Model_Config_Source_Display
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        /**
         * VAT is not applicable to FPT separately (we can't have FPT incl/excl VAT)
         */
        return [
            [
                'value' => 0,
                'label' => Mage::helper('weee')->__('Including FPT only'),
            ],
            [
                'value' => 1,
                'label' => Mage::helper('weee')->__('Including FPT and FPT description'),
            ],
            //array('value'=>4, 'label'=>Mage::helper('weee')->__('Including FPT and FPT description [incl. FPT VAT]')),
            [
                'value' => 2,
                'label' => Mage::helper('weee')->__('Excluding FPT, FPT description, final price'),
            ],
            [
                'value' => 3,
                'label' => Mage::helper('weee')->__('Excluding FPT'),
            ],
        ];
    }
}
