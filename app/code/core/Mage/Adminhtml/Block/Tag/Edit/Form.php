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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml tag edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
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
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('tag')->__('General Information')));

        if ($model->getTagId()) {
            $fieldset->addField('tag_id', 'hidden', array(
                'name' => 'tag_id',
            ));
        }

        $fieldset->addField('form_key', 'hidden', array(
            'name'  => 'form_key',
            'value' => Mage::getSingleton('core/session')->getFormKey(),
        ));

        $fieldset->addField('store_id', 'hidden', array(
            'name'  => 'store_id',
            'value' => (int)$this->getRequest()->getParam('store')
        ));

        $fieldset->addField('name', 'text', array(
            'name' => 'tag_name',
            'label' => Mage::helper('tag')->__('Tag Name'),
            'title' => Mage::helper('tag')->__('Tag Name'),
            'required' => true,
            'after_element_html' => ' ' . Mage::helper('adminhtml')->__('[GLOBAL]'),
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('tag')->__('Status'),
            'title' => Mage::helper('tag')->__('Status'),
            'name' => 'tag_status',
            'required' => true,
            'options' => array(
                Mage_Tag_Model_Tag::STATUS_DISABLED => Mage::helper('tag')->__('Disabled'),
                Mage_Tag_Model_Tag::STATUS_PENDING  => Mage::helper('tag')->__('Pending'),
                Mage_Tag_Model_Tag::STATUS_APPROVED => Mage::helper('tag')->__('Approved'),
            ),
            'after_element_html' => ' ' . Mage::helper('adminhtml')->__('[GLOBAL]'),
        ));

        $fieldset->addField('base_popularity', 'text', array(
            'name' => 'base_popularity',
            'label' => Mage::helper('tag')->__('Base Popularity'),
            'title' => Mage::helper('tag')->__('Base Popularity'),
            'after_element_html' => ' ' . Mage::helper('tag')->__('[STORE VIEW]'),
        ));

        if (!$model->getId() && !Mage::getSingleton('adminhtml/session')->getTagData() ) {
            $model->setStatus(Mage_Tag_Model_Tag::STATUS_APPROVED);
        }

        if ( Mage::getSingleton('adminhtml/session')->getTagData() ) {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getTagData());
            Mage::getSingleton('adminhtml/session')->setTagData(null);
        } else {
            $form->addValues($model->getData());
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
