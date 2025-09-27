<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml tag edit form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Tag_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_form');
        $this->setTitle(Mage::helper('tag')->__('Block Information'));
    }

    /**
     * Prepare form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('tag_tag');

        $form = new Varien_Data_Form(
            ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'],
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => Mage::helper('tag')->__('General Information')],
        );

        if ($model->getTagId()) {
            $fieldset->addField('tag_id', 'hidden', [
                'name' => 'tag_id',
            ]);
        }

        $fieldset->addField('form_key', 'hidden', [
            'name'  => 'form_key',
            'value' => Mage::getSingleton('core/session')->getFormKey(),
        ]);

        $fieldset->addField('store_id', 'hidden', [
            'name'  => 'store_id',
            'value' => (int) $this->getRequest()->getParam('store'),
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'tag_name',
            'label' => Mage::helper('tag')->__('Tag Name'),
            'title' => Mage::helper('tag')->__('Tag Name'),
            'required' => true,
            'after_element_html' => ' ' . Mage::helper('adminhtml')->__('[GLOBAL]'),
        ]);

        $fieldset->addField('status', 'select', [
            'label' => Mage::helper('tag')->__('Status'),
            'title' => Mage::helper('tag')->__('Status'),
            'name' => 'tag_status',
            'required' => true,
            'options' => [
                Mage_Tag_Model_Tag::STATUS_DISABLED => Mage::helper('tag')->__('Disabled'),
                Mage_Tag_Model_Tag::STATUS_PENDING  => Mage::helper('tag')->__('Pending'),
                Mage_Tag_Model_Tag::STATUS_APPROVED => Mage::helper('tag')->__('Approved'),
            ],
            'after_element_html' => ' ' . Mage::helper('adminhtml')->__('[GLOBAL]'),
        ]);

        $fieldset->addField('base_popularity', 'text', [
            'name' => 'base_popularity',
            'label' => Mage::helper('tag')->__('Base Popularity'),
            'title' => Mage::helper('tag')->__('Base Popularity'),
            'after_element_html' => ' ' . Mage::helper('tag')->__('[STORE VIEW]'),
        ]);

        if (!$model->getId() && !Mage::getSingleton('adminhtml/session')->getTagData()) {
            $model->setStatus(Mage_Tag_Model_Tag::STATUS_APPROVED);
        }

        if (Mage::getSingleton('adminhtml/session')->getTagData()) {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getTagData());
            Mage::getSingleton('adminhtml/session')->setTagData(null);
        } else {
            $form->addValues($model->getData());
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
