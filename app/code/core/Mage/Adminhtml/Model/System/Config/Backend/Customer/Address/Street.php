<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Customer Address Street Model
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Customer_Address_Street extends Mage_Core_Model_Config_Data
{
    /**
     * Actions after save
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $attribute = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'street');
        $value  = $this->getValue();
        switch ($this->getScope()) {
            case 'websites':
                $website = Mage::app()->getWebsite($this->getWebsiteCode());
                $attribute->setWebsite($website);
                $attribute->load($attribute->getId());
                if ($attribute->getData('multiline_count') != $value) {
                    $attribute->setData('scope_multiline_count', $value);
                }

                break;

            case 'default':
                $attribute->setData('multiline_count', $value);
                break;
        }

        $attribute->save();
        return $this;
    }

    /**
     * Processing object after delete data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterDelete()
    {
        $result = parent::_afterDelete();

        if ($this->getScope() == 'websites') {
            $attribute = Mage::getSingleton('eav/config')->getAttribute('customer_address', 'street');
            $website = Mage::app()->getWebsite($this->getWebsiteCode());
            $attribute->setWebsite($website);
            $attribute->load($attribute->getId());
            $attribute->setData('scope_multiline_count', null);
            $attribute->save();
        }

        return $result;
    }
}
