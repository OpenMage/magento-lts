<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Currency edit form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Currency_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('currency_edit_form');
        $this->setTitle(Mage::helper('adminhtml')->__('Currency Information'));
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(['id' => 'currency_edit_form', 'action' => $this->getData('action'), 'method' => 'post']);
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
