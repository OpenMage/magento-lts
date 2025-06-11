<?php
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