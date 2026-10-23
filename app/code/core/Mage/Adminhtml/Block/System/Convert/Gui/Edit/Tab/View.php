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
class Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tab_View extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return $this
     */
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_view');

        $model = $this->getRegistryCurrentConvertProfile();

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => Mage::helper('adminhtml')->__('View Actions XML'),
            'class' => 'fieldset-wide',
        ]);

        $fieldset->addField('actions_xml', 'textarea', [
            'name' => 'actions_xml_view',
            'label' => Mage::helper('adminhtml')->__('Actions XML'),
            'title' => Mage::helper('adminhtml')->__('Actions XML'),
            'style' => 'height:30em',
            'readonly' => 'readonly',
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
