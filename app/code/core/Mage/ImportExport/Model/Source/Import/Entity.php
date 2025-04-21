<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Source import entity model
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Source_Import_Entity
{
    /**
     * Prepare and return array of import entities ids and their names
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $entities = Mage_ImportExport_Model_Import::CONFIG_KEY_ENTITIES;
        $comboOptions = Mage_ImportExport_Model_Config::getModelsComboOptions($entities);

        foreach ($comboOptions as $option) {
            $options[] = $option;
        }
        return $options;
    }
}
