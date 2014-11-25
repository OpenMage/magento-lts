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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Custom Variable Edit Form
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Variable_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Getter
     *
     * @return Mage_Core_Model_Variable
     */
    public function getVariable()
    {
        return Mage::registry('current_variable');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_System_Variable_Edit_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base', array(
            'legend'=>Mage::helper('adminhtml')->__('Variable'),
            'class'=>'fieldset-wide'
        ));

        $fieldset->addField('code', 'text', array(
            'name'     => 'code',
            'label'    => Mage::helper('adminhtml')->__('Variable Code'),
            'title'    => Mage::helper('adminhtml')->__('Variable Code'),
            'required' => true,
            'class'    => 'validate-xml-identifier'
        ));

        $fieldset->addField('name', 'text', array(
            'name'     => 'name',
            'label'    => Mage::helper('adminhtml')->__('Variable Name'),
            'title'    => Mage::helper('adminhtml')->__('Variable Name'),
            'required' => true
        ));

        $useDefault = false;
        if ($this->getVariable()->getId() && $this->getVariable()->getStoreId()) {
            $useDefault = !((bool)$this->getVariable()->getStoreHtmlValue());
            $this->getVariable()->setUseDefaultValue((int)$useDefault);
            $fieldset->addField('use_default_value', 'select', array(
                'name'   => 'use_default_value',
                'label'  => Mage::helper('adminhtml')->__('Use Default Variable Values'),
                'title'  => Mage::helper('adminhtml')->__('Use Default Variable Values'),
                'onchange' => 'toggleValueElement(this);',
                'values' => array(
                    0 => Mage::helper('adminhtml')->__('No'),
                    1 => Mage::helper('adminhtml')->__('Yes')
                )
            ));
        }

        $fieldset->addField('html_value', 'textarea', array(
            'name'     => 'html_value',
            'label'    => Mage::helper('adminhtml')->__('Variable HTML Value'),
            'title'    => Mage::helper('adminhtml')->__('Variable HTML Value'),
            'disabled' => $useDefault
        ));

        $fieldset->addField('plain_value', 'textarea', array(
            'name'     => 'plain_value',
            'label'    => Mage::helper('adminhtml')->__('Variable Plain Value'),
            'title'    => Mage::helper('adminhtml')->__('Variable Plain Value'),
            'disabled' => $useDefault
        ));

        $form->setValues($this->getVariable()->getData())
            ->addFieldNameSuffix('variable')
            ->setUseContainer(true);

        $this->setForm($form);
        return parent::_prepareForm();
    }

}
