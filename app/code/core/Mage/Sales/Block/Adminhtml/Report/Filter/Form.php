<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales Adminhtml report filter form
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Block_Adminhtml_Report_Filter_Form extends Mage_Adminhtml_Block_Report_Filter_Form
{
    /**
     * Add fields to base fieldset which are general to sales reports
     *
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
            $statuses = Mage::getModel('sales/order_config')->getStatuses();
            $values = [];
            foreach ($statuses as $code => $label) {
                if (strpos($code, 'pending') === false) {
                    $values[] = [
                        'label' => Mage::helper('reports')->__($label),
                        'value' => $code
                    ];
                }
            }

            $fieldset->addField('show_order_statuses', 'select', [
                'name'      => 'show_order_statuses',
                'label'     => Mage::helper('reports')->__('Order Status'),
                'options'   => [
                        '0' => Mage::helper('reports')->__('Any'),
                        '1' => Mage::helper('reports')->__('Specified'),
                ],
                'note'      => Mage::helper('reports')->__('Applies to Any of the Specified Order Statuses'),
            ], 'to');

            $fieldset->addField('order_statuses', 'multiselect', [
                'name'      => 'order_statuses',
                'values'    => $values,
                'display'   => 'none'
            ], 'show_order_statuses');

            // define field dependencies
            /** @var Mage_Adminhtml_Block_Widget_Form_Element_Dependence $block */
            $block = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');
            if ($this->getFieldVisibility('show_order_statuses') && $this->getFieldVisibility('order_statuses')) {
                $this->setChild('form_after', $block
                    ->addFieldMap("{$htmlIdPrefix}show_order_statuses", 'show_order_statuses')
                    ->addFieldMap("{$htmlIdPrefix}order_statuses", 'order_statuses')
                    ->addFieldDependence('order_statuses', 'show_order_statuses', '1'));
            }
        }

        return $this;
    }
}
