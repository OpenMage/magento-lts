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
 * @deprecated after 1.3.2.3
 */
class Mage_Adminhtml_Block_Tag_Tag_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_form');
        $this->setTitle(Mage::helper('tag')->__('Block Information'));
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('tag_tag');

        $form = new Varien_Data_Form([
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id'), 'ret' => Mage::registry('ret')]),
            'method' => 'post',
        ]);

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('tag')->__('General Information')]);

        if ($model->getTagId()) {
            $fieldset->addField('tag_id', 'hidden', [
                'name' => 'tag_id',
            ]);
        }

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => Mage::helper('tag')->__('Tag Name'),
            'title' => Mage::helper('tag')->__('Tag Name'),
            'required' => true,
        ]);

        $fieldset->addField('status', 'select', [
            'label' => Mage::helper('tag')->__('Status'),
            'title' => Mage::helper('tag')->__('Status'),
            'name' => 'status',
            'required' => true,
            'options' => [
                Mage_Tag_Model_Tag::STATUS_DISABLED => Mage::helper('tag')->__('Disabled'),
                Mage_Tag_Model_Tag::STATUS_PENDING  => Mage::helper('tag')->__('Pending'),
                Mage_Tag_Model_Tag::STATUS_APPROVED => Mage::helper('tag')->__('Approved'),
            ],
        ]);

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $form->setAction($this->getUrl($form->getAction(), [
            'ret' => $this->getRequest()->getParam('ret'),
            'customer_id' => $this->getRequest()->getParam('customer_id'),
            'product_id' => $this->getRequest()->getParam('product_id'),
        ]));
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
