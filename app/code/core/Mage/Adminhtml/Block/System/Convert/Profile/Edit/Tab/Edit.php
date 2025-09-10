<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Convert profile edit tab
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tab_Edit extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return $this
     */
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_edit');

        $model = $this->getRegistryCurrentConvertProfile();

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => Mage::helper('adminhtml')->__('General Information'),
            'class' => 'fieldset-wide',
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => Mage::helper('adminhtml')->__('Profile Name'),
            'title' => Mage::helper('adminhtml')->__('Profile Name'),
            'required' => true,
        ]);

        $fieldset->addField('actions_xml', 'textarea', [
            'name' => 'actions_xml',
            'label' => Mage::helper('adminhtml')->__('Actions XML'),
            'title' => Mage::helper('adminhtml')->__('Actions XML'),
            'style' => 'height:30em',
            'required' => true,
        ]);

        $form->setValues($model->getData());

        $this->setForm($form);

        return $this;
    }

    protected function getRegistryCurrentConvertProfile(): ?Mage_Dataflow_Model_Profile
    {
        return Mage::registry('current_convert_profile');
    }
}
