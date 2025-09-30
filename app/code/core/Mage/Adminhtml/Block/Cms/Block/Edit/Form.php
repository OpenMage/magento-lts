<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml cms block edit form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cms_Block_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('block_form');
        $this->setTitle(Mage::helper('cms')->__('Block Information'));
    }

    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        return $this;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('cms_block');

        $form = new Varien_Data_Form(
            ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'],
        );

        $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('cms')->__('General Information'), 'class' => 'fieldset-wide']);

        if ($model->getBlockId()) {
            $fieldset->addField('block_id', 'hidden', [
                'name' => 'block_id',
            ]);
        }

        $fieldset->addField('title', 'text', [
            'name'      => 'title',
            'label'     => Mage::helper('cms')->__('Block Title'),
            'title'     => Mage::helper('cms')->__('Block Title'),
            'required'  => true,
        ]);

        $fieldset->addField('identifier', 'text', [
            'name'      => 'identifier',
            'label'     => Mage::helper('cms')->__('Identifier'),
            'title'     => Mage::helper('cms')->__('Identifier'),
            'required'  => true,
            'class'     => 'validate-xml-identifier',
        ]);

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'multiselect', [
                'name'      => 'stores[]',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ]);
            $renderer = $this->getStoreSwitcherRenderer();
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', [
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId(),
            ]);
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('is_active', 'select', [
            'label'     => Mage::helper('cms')->__('Status'),
            'title'     => Mage::helper('cms')->__('Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => [
                '1' => Mage::helper('cms')->__('Enabled'),
                '0' => Mage::helper('cms')->__('Disabled'),
            ],
        ]);
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $fieldset->addField('content', 'editor', [
            'name'      => 'content',
            'label'     => Mage::helper('cms')->__('Content'),
            'title'     => Mage::helper('cms')->__('Content'),
            'style'     => 'height:36em',
            'required'  => true,
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
        ]);

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
