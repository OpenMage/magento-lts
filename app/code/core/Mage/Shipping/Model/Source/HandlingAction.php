<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Shipping
 */
class Mage_Shipping_Model_Source_HandlingAction
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Mage_Shipping_Model_Carrier_Abstract::HANDLING_ACTION_PERORDER, 'label' => Mage::helper('shipping')->__('Per Order')],
            ['value' => Mage_Shipping_Model_Carrier_Abstract::HANDLING_ACTION_PERPACKAGE , 'label' => Mage::helper('shipping')->__('Per Package')],
        ];
    }
}
