<?php
/**
 * Source model of export formats
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_ImportExport
 */
class Mage_ImportExport_Model_Source_Export_Format
{
    /**
     * Prepare and return array of available export file formats.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $formats = Mage_ImportExport_Model_Export::CONFIG_KEY_FORMATS;
        return Mage_ImportExport_Model_Config::getModelsComboOptions($formats);
    }
}
