<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

class Mage_Paypal_Model_System_Config_Backend_MerchantCountry extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (!$value) {
            $this->setValue(Mage::getStoreConfig('general/country/default'));
        }
        return parent::_beforeSave();
    }
}
