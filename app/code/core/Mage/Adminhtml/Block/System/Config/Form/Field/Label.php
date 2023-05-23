<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml system config label field renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Label extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $field = $element->getFieldConfig();

        $type = $element->getType();
        if ($type !== 'label' && $type !== 'text') {
            Mage::throwException(
                Mage::helper('adminhtml')->__(
                    'Invalid frontend type for field "%s". Onyl "text" and "label" are allowed.',
                    $field->descend('label')
                )
            );
        }

        $label = new Varien_Data_Form_Element_Label();
        $label->setValue($field->descend('value'));
        $label->setBold(!empty($field->descend('bold')));

        return $label->getElementHtml();
    }
}
