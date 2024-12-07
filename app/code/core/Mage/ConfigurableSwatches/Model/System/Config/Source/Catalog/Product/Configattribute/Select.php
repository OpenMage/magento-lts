<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 */
class Mage_ConfigurableSwatches_Model_System_Config_Source_Catalog_Product_Configattribute_Select extends Mage_ConfigurableSwatches_Model_System_Config_Source_Catalog_Product_Configattribute
{
    /**
     * Retrieve attributes as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (is_null($this->_attributes)) {
            parent::toOptionArray();
            array_unshift(
                $this->_attributes,
                [
                    'value' => '',
                    'label' => Mage::helper('configurableswatches')->__('-- Please Select --'),
                ],
            );
        }
        return $this->_attributes;
    }
}
