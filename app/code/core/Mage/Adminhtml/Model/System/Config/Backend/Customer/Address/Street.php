<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer Address Street Model
 *
 * @category   Mage
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
