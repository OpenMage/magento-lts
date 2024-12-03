<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Price_Step
{
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_Catalog_Model_Layer_Filter_Price::RANGE_CALCULATION_AUTO,
                'label' => Mage::helper('adminhtml')->__('Automatic (equalize price ranges)')
            ],
            [
                'value' => Mage_Catalog_Model_Layer_Filter_Price::RANGE_CALCULATION_IMPROVED,
                'label' => Mage::helper('adminhtml')->__('Automatic (equalize product counts)')
            ],
            [
                'value' => Mage_Catalog_Model_Layer_Filter_Price::RANGE_CALCULATION_MANUAL,
                'label' => Mage::helper('adminhtml')->__('Manual')
            ],
        ];
    }
}
