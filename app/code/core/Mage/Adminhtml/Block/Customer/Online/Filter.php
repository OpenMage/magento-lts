<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml customers online filter
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Online_Filter extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->addField(
            'filter_value',
            'select',
            [
                'name' => 'filter_value',
                'onchange' => 'this.form.submit()',
                'values' => [
                    [
                        'label' => Mage::helper('customer')->__('All'),
                        'value' => '',
                    ],

                    [
                        'label' => Mage::helper('customer')->__('Customers Only'),
                        'value' => 'filterCustomers',
                    ],

                    [
                        'label' => Mage::helper('customer')->__('Visitors Only'),
                        'value' => 'filterGuests',
                    ],
                ],
                'no_span' => true,
            ],
        );

        $form->setUseContainer(true);
        $form->setId('filter_form');
        $form->setMethod('post');

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
