<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Edit status form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Sales_Order_Status_Edit_Form extends Mage_Adminhtml_Block_Sales_Order_Status_New_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('new_order_status');
    }

    /**
     * Modify structure of new status form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->getForm();
        $form->getElement('base_fieldset')->removeField('is_new');
        $form->getElement('base_fieldset')->removeField('status');
        $form->setAction(
            $this->getUrl('*/sales_order_status/save', ['status' => $this->getRequest()->getParam('status')]),
        );
        return $this;
    }
}
