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
 * @category    Mage
 * @package     Mage_Eav
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Eav_Block_Adminhtml_Attribute_Set_Main_Formset extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Prepares attribute set form
     *
     */
    protected function _prepareForm()
    {
        $data = Mage::getModel('eav/entity_attribute_set')
            ->load($this->getRequest()->getParam('id'));

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('set_name', ['legend'=> Mage::helper('eav')->__('Edit Set Name')]);
        $fieldset->addField('attribute_set_name', 'text', [
            'label' => Mage::helper('eav')->__('Name'),
            'note' => Mage::helper('eav')->__('For internal use.'),
            'name' => 'attribute_set_name',
            'required' => true,
            'class' => 'required-entry validate-no-html-tags',
            'value' => $data->getAttributeSetName()
        ]);

        if (!$this->getRequest()->getParam('id', false)) {
            $fieldset->addField('gotoEdit', 'hidden', [
                'name' => 'gotoEdit',
                'value' => '1'
            ]);

            /** @var Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection @collection */
            $collection = Mage::getModel('eav/entity_attribute_set')
                        ->getResourceCollection();

            $sets = $collection->setEntityTypeFilter(Mage::registry('entity_type')->getEntityTypeId())
                ->setOrder('attribute_set_name', 'asc')
                ->load()
                ->toOptionArray();

            $fieldset->addField('skeleton_set', 'select', [
                'label' => Mage::helper('eav')->__('Based On'),
                'name' => 'skeleton_set',
                'required' => true,
                'class' => 'required-entry',
                'values' => $sets,
            ]);
        }

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('set_prop_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setOnsubmit('return false;');
        $this->setForm($form);
        return $this;
    }
}
