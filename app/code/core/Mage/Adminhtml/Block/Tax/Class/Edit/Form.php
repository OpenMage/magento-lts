<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml Tax Class Edit Form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tax_Class_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('taxClassForm');
    }

    protected function _prepareForm()
    {
        $model  = Mage::registry('tax_class');
        $form   = new Varien_Data_Form([
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post',
        ]);

        $classType  = $this->getClassType();

        $this->setTitle($classType == Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER
            ? Mage::helper('cms')->__('Customer Tax Class Information')
            : Mage::helper('cms')->__('Product Tax Class Information'));

        $fieldset   = $form->addFieldset('base_fieldset', [
            'legend'    => $classType == Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER
                ? Mage::helper('tax')->__('Customer Tax Class Information')
                : Mage::helper('tax')->__('Product Tax Class Information'),
        ]);

        $fieldset->addField(
            'class_name',
            'text',
            [
                'name'  => 'class_name',
                'label' => Mage::helper('tax')->__('Class Name'),
                'class' => 'required-entry',
                'value' => $model->getClassName(),
                'required' => true,
            ],
        );

        $fieldset->addField(
            'class_type',
            'hidden',
            [
                'name'      => 'class_type',
                'value'     => $classType,
                'no_span'   => true,
            ],
        );

        if ($model->getId()) {
            $fieldset->addField(
                'class_id',
                'hidden',
                [
                    'name'      => 'class_id',
                    'value'     => $model->getId(),
                    'no_span'   => true,
                ],
            );
        }

        $form->setAction($this->getUrl('*/tax_class/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
