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
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
