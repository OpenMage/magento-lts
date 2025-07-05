<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Source import behavior model
 *
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
