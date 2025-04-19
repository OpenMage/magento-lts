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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Sales Model Observer
 *
 * @category   Mage
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Observer_PrepareProductEditFormRecurringProfile implements Mage_Core_Observer_Interface
{
    /**
     * Add the recurring profile form when editing a product
     *
     * @param Varien_Event_Observer $observer
     */
    public function execute($observer): self
    {
        // replace the element of recurring payment profile field with a form
        /** @var Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element $profileElement */
        $profileElement = $observer->getEvent()->getDataByKey('product_element');
        /** @var stdClass $result */
        $result = $observer->getEvent()->getDataByKey('result');

        $block = Mage::app()->getLayout()->createBlock(
            'sales/adminhtml_recurring_profile_edit_form',
            'adminhtml_recurring_profile_edit_form',
        )->setParentElement($profileElement)
            ->setProductEntity($observer->getEvent()->getProduct());
        $result->output = $block->toHtml();

        // make the profile element dependent on is_recurring
        /** @var Mage_Adminhtml_Block_Widget_Form_Element_Dependence $block */
        $block = Mage::app()->getLayout()->createBlock(
            'adminhtml/widget_form_element_dependence',
            'adminhtml_recurring_profile_edit_form_dependence',
        );
        $dependencies = $block
            ->addFieldMap('is_recurring', 'product[is_recurring]')
            ->addFieldMap($profileElement->getHtmlId(), $profileElement->getName())
            ->addFieldDependence($profileElement->getName(), 'product[is_recurring]', '1');
        $result->output .= $dependencies->toHtml();

        return $this;
    }
}
