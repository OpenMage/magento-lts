<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Tax Rule Edit Form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Tax_Rule_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init class
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('taxRuleForm');
        $this->setTitle(Mage::helper('tax')->__('Tax Rule Information'));
    }

    /**
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model  = Mage::registry('tax_rule');
        $form   = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('tax')->__('Tax Rule Information')
        ));

        $productClasses = Mage::getModel('tax/class')
            ->getCollection()
            ->setClassTypeFilter('PRODUCT')
            ->toOptionArray();

        $customerClasses = Mage::getModel('tax/class')
            ->getCollection()
            ->setClassTypeFilter('CUSTOMER')
            ->toOptionArray();

        $rates = Mage::getModel('tax/calculation_rate')
            ->getCollection()
            ->toOptionArray();

        $fieldset->addField('code', 'text',
            array(
                'name'      => 'code',
                'label'     => Mage::helper('tax')->__('Name'),
                'class'     => 'required-entry',
                'required'  => true,
            )
        );

        $fieldset->addField('tax_customer_class', 'multiselect',
            array(
                'name'      => 'tax_customer_class',
                'label'     => Mage::helper('tax')->__('Customer Tax Class'),
                'class'     => 'required-entry',
                'values'    => $customerClasses,
                'value'     => $model->getCustomerTaxClasses(),
                'required'  => true,
            )
        );

        $fieldset->addField('tax_product_class', 'multiselect',
            array(
                'name'      => 'tax_product_class',
                'label'     => Mage::helper('tax')->__('Product Tax Class'),
                'class'     => 'required-entry',
                'values'    => $productClasses,
                'value'     => $model->getProductTaxClasses(),
                'required'  => true,
            )
        );

        $fieldset->addField('tax_rate', 'multiselect',
            array(
                'name'      => 'tax_rate',
                'label'     => Mage::helper('tax')->__('Tax Rate'),
                'class'     => 'required-entry',
                'values'    => $rates,
                'value'     => $model->getRates(),
                'required'  => true,
            )
        );
        $fieldset->addField('priority', 'text',
            array(
                'name'      => 'priority',
                'label'     => Mage::helper('tax')->__('Priority'),
                'class'     => 'validate-not-negative-number',
                'value'     => (int) $model->getPriority(),
                'required'  => true,
                'note'      => Mage::helper('tax')->__('Tax rates at the same priority are added, others are compounded.'),
            )
        );
        $fieldset->addField('position', 'text',
            array(
                'name'      => 'position',
                'label'     => Mage::helper('tax')->__('Sort Order'),
                'class'     => 'validate-not-negative-number',
                'value'     => (int) $model->getPosition(),
                'required'  => true,
            )
        );

        if ($model->getId() > 0 ) {
            $fieldset->addField('tax_calculation_rule_id', 'hidden',
                array(
                    'name'      => 'tax_calculation_rule_id',
                    'value'     => $model->getId(),
                    'no_span'   => true
                )
            );
        }

        $form->addValues($model->getData());
        $form->setAction($this->getUrl('*/tax_rule/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}