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
 *
 * Usa Ups type action Dropdown source
 *
 * @category   Mage
 * @package    Mage_Usa
 */
class Mage_Usa_Model_Shipping_Carrier_Ups_Source_OriginShipment
{
    public function toOptionArray()
    {
        $orShipArr = Mage::getSingleton('usa/shipping_carrier_ups')->getCode('originShipment');
        $returnArr = [];
        foreach ($orShipArr as $key => $val) {
            $returnArr[] = ['value' => $key,'label' => $key];
        }
        return $returnArr;
    }
}
