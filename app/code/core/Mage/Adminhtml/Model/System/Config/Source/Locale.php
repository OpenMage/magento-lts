<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Locale source
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Locale
{
    public function toOptionArray()
    {
        return Mage::app()->getLocale()->getOptionLocales();
    }
}
