<?php
/**
 * Customer Show Address Model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Backend_Customer_Show_Address extends Mage_Adminhtml_Model_System_Config_Backend_Customer_Show_Customer
{
    /**
     * Retrieve attribute objects
     *
     * @return array
     */
    protected function _getAttributeObjects()
    {
        $result = parent::_getAttributeObjects();
        $result[] = Mage::getSingleton('eav/config')->getAttribute('customer_address', $this->_getAttributeCode());
        return $result;
    }
}
