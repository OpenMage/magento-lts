<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Usa
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * UPS (UPS XML) mode source model
 *
 * @category   Mage
 * @package    Mage_Usa
 * @deprecated  since 1.7.0.0
 */
class Mage_Usa_Model_Shipping_Carrier_Ups_Source_Mode
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => Mage::helper('usa')->__('Live')],
            ['value' => '0', 'label' => Mage::helper('usa')->__('Development')],
        ];
    }
}
