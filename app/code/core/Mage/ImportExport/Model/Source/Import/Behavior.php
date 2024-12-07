<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Source import behavior model
 *
 * @category   Mage
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Source_Import_Behavior
{
    /**
     * Prepare and return array of import behavior.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => Mage_ImportExport_Model_Import::BEHAVIOR_APPEND,
                'label' => Mage::helper('importexport')->__('Append Complex Data'),
            ],
            [
                'value' => Mage_ImportExport_Model_Import::BEHAVIOR_REPLACE,
                'label' => Mage::helper('importexport')->__('Replace Existing Complex Data'),
            ],
            [
                'value' => Mage_ImportExport_Model_Import::BEHAVIOR_DELETE,
                'label' => Mage::helper('importexport')->__('Delete Entities'),
            ],
        ];
    }
}
