<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_ImportExport
 */

/**
 * Source import entity model
 *
 * @category   Mage
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
