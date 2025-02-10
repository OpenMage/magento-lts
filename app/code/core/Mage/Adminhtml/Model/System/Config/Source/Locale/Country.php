<?php
/**
 * Locale country source
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Locale_Country
{
    public function toOptionArray()
    {
        return Mage::app()->getLocale()->getOptionCountries();
    }
}
