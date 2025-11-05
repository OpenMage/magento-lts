<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Export CSV button for shipping table rates
 *
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
            'onclick'   => "setLocation('" . Mage::helper('adminhtml')->getUrl('*/*/exportTablerates', $params) . 'conditionName/\' + $(\'carriers_tablerate_condition_name\').value + \'/tablerates.csv\' )',
            'class'     => '',
        ];

        return $buttonBlock->setData($data)->toHtml();
    }
}
