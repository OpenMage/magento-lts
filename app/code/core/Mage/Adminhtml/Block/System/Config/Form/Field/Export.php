<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Export CSV button for shipping table rates
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Export extends Varien_Data_Form_Element_Abstract
{
    /**
     * @return string
     */
    public function getElementHtml()
    {
        $buttonBlock = $this->getForm()->getParent()->getLayout()->createBlock('adminhtml/widget_button');

        $params = [
            'website' => $buttonBlock->getRequest()->getParam('website'),
        ];

        $data = [
            'label'     => Mage::helper('adminhtml')->__('Export CSV'),
            'onclick'   => 'setLocation(\'' . Mage::helper('adminhtml')->getUrl('*/*/exportTablerates', $params) . 'conditionName/\' + $(\'carriers_tablerate_condition_name\').value + \'/tablerates.csv\' )',
            'class'     => '',
        ];

        return $buttonBlock->setData($data)->toHtml();
    }
}
