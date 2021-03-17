<?php
/**
 * OpenMage
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009-2021 OpenMage (https://www.openmage.org/)
 */
class Mage_Adminhtml_Model_System_Config_Source_Customer_Vatnumberformat
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Without country code')),
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('With country code')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('With and without country code')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            0 => Mage::helper('adminhtml')->__('Without country code'),
            1 => Mage::helper('adminhtml')->__('With country code'),
            2 => Mage::helper('adminhtml')->__('With and without country code'),
        );
    }
}
