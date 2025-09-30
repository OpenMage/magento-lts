<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Source export entity model
 *
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Source_Export_Entity
{
    /**
     * Prepare and return array of export entities ids and their names
     *
     * @return array
     */
    public function toOptionArray()
    {
        return Mage_ImportExport_Model_Config::getModelsComboOptions(
            Mage_ImportExport_Model_Export::CONFIG_KEY_ENTITIES,
            true,
        );
    }
}
