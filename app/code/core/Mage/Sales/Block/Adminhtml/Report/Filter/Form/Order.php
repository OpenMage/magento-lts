<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Sales
 */

/**
 * Sales Adminhtml report filter form order
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Block_Adminhtml_Report_Filter_Form_Order extends Mage_Sales_Block_Adminhtml_Report_Filter_Form
{
    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();
        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');

        if (is_object($fieldset) && $fieldset instanceof Varien_Data_Form_Element_Fieldset) {
            $fieldset->addField('show_actual_columns', 'select', [
                'name'       => 'show_actual_columns',
                'options'    => [
                    '1' => Mage::helper('reports')->__('Yes'),
                    '0' => Mage::helper('reports')->__('No'),
                ],
                'label'      => Mage::helper('reports')->__('Show Actual Values'),
            ]);
        }

        return $this;
    }
}
