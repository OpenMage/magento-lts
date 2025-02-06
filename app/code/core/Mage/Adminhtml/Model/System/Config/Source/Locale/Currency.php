<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Locale currency source
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Locale_Currency
{
    protected $_option;

    public function toOptionArray()
    {
        return Mage::app()->getLocale()->getOptionCurrencies();
    }
}
