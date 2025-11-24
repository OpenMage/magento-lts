<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Customer Show Customer Model
 *
 * @package    Mage_Adminhtml
 *
 * @method string getField()
 */
class Mage_Adminhtml_Model_System_Config_Backend_Customer_Show_Customer extends Mage_Core_Model_Config_Data
{
    /**
     * Retrieve attribute code
     *
     * @return string
     */
    protected function _getAttributeCode()
    {
        return str_replace('_show', '', $this->getField());
    }

    /**
     * Retrieve attribute objects
     *
     * @return array
     */
    protected function _getAttributeObjects()
    {
        return [
            Mage::getSingleton('eav/config')->getAttribute('customer', $this->_getAttributeCode()),
        ];
    }

    /**
     * Actions after save
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $result = parent::_afterSave();

        $valueConfig = [
            ''    => ['is_required' => 0, 'is_visible' => 0],
            'opt' => ['is_required' => 0, 'is_visible' => 1],
            '1'   => ['is_required' => 0, 'is_visible' => 1],
            'req' => ['is_required' => 1, 'is_visible' => 1],
        ];

        $value = $this->getValue();
        $data = $valueConfig[$value] ?? $valueConfig[''];

        if ($this->getScope() == 'websites') {
            $website = Mage::app()->getWebsite($this->getWebsiteCode());
            $dataFieldPrefix = 'scope_';
        } else {
            $website = null;
            $dataFieldPrefix = '';
        }

        foreach ($this->_getAttributeObjects() as $attributeObject) {
            if ($website) {
                $attributeObject->setWebsite($website);
                $attributeObject->load($attributeObject->getId());
            }

            $attributeObject->setData($dataFieldPrefix . 'is_required', $data['is_required']);
            $attributeObject->setData($dataFieldPrefix . 'is_visible', $data['is_visible']);
            $attributeObject->save();
        }

        return $result;
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
            $website = Mage::app()->getWebsite($this->getWebsiteCode());
            foreach ($this->_getAttributeObjects() as $attributeObject) {
                $attributeObject->setWebsite($website);
                $attributeObject->load($attributeObject->getId());
                $attributeObject->setData('scope_is_required', null);
                $attributeObject->setData('scope_is_visible', null);
                $attributeObject->save();
            }
        }

        return $result;
    }
}
