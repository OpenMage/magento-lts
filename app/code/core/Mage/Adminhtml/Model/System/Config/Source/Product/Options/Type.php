<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product option types mode source
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Source_Product_Options_Type
{
    public function toOptionArray()
    {
        return array(
            array('value' => '', 'label' => Mage::helper('adminhtml')->__('-- Please select --')),
            array(
                'label' => Mage::helper('adminhtml')->__('Text'),
                'value' => array(
                    array('value' => 'field', 'label' => Mage::helper('adminhtml')->__('Field')),
                    array('value' => 'area', 'label' => Mage::helper('adminhtml')->__('Area')),
                )
            ),
//            array(
//                'label' => Mage::helper('adminhtml')->__('File'),
//                'value' => array(
//                    array('value' => 'file', 'label' => Mage::helper('adminhtml')->__('File')),
//                )
//            ),
            array(
                'label' => Mage::helper('adminhtml')->__('Select'),
                'value' => array(
                    array('value' => 'drop_down', 'label' => Mage::helper('adminhtml')->__('Drop-down')),
                    array('value' => 'radio', 'label' => Mage::helper('adminhtml')->__('Radio Buttons')),
                    array('value' => 'checkbox', 'label' => Mage::helper('adminhtml')->__('Checkbox')),
                    array('value' => 'multiple', 'label' => Mage::helper('adminhtml')->__('Multiple Select')),
                )
            ),
//            array(
//                'label' => Mage::helper('adminhtml')->__('Date'),
//                'value' => array(
//                    array('value' => 'date', 'label' => Mage::helper('adminhtml')->__('Date')),
//                    array('value' => 'date_time', 'label' => Mage::helper('adminhtml')->__('Date & Time')),
//                    array('value' => 'time', 'label' => Mage::helper('adminhtml')->__('Time'))
//                )
//            )
        );
    }
}