<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Extended Attributes Source Model
 *
 * @category   Mage
 * @package    Mage_Bundle
 */
class Mage_Bundle_Model_Source_Option_Selection_Price_Type
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => Mage::helper('bundle')->__('Fixed')],
            ['value' => '1', 'label' => Mage::helper('bundle')->__('Percent')],
        ];
    }
}
