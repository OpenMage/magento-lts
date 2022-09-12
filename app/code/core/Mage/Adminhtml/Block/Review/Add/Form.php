<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml add product review form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Review_Add_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('add_review_form', ['legend' => Mage::helper('review')->__('Review Details')]);

        $fieldset->addField('product_name', 'note', [
            'label'     => Mage::helper('review')->__('Product'),
            'text'      => 'product_name',
        ]);

        $fieldset->addField('detailed_rating', 'note', [
            'label'     => Mage::helper('review')->__('Product Rating'),
            'required'  => true,
            'text'      => '<div id="rating_detail">'
                . $this->getLayout()->createBlock('adminhtml/review_rating_detailed')->toHtml() . '</div>',
        ]);

        $fieldset->addField('status_id', 'select', [
            'label'     => Mage::helper('review')->__('Status'),
            'required'  => true,
            'name'      => 'status_id',
            'values'    => Mage::helper('review')->getReviewStatusesOptionArray(),
        ]);

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('select_stores', 'multiselect', [
                'label'     => Mage::helper('review')->__('Visible In'),
                'required'  => true,
                'name'      => 'select_stores[]',
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
            ]);
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('nickname', 'text', [
            'name'      => 'nickname',
            'title'     => Mage::helper('review')->__('Nickname'),
            'label'     => Mage::helper('review')->__('Nickname'),
            'maxlength' => '50',
            'required'  => true,
        ]);

        $fieldset->addField('title', 'text', [
            'name'      => 'title',
            'title'     => Mage::helper('review')->__('Summary of Review'),
            'label'     => Mage::helper('review')->__('Summary of Review'),
            'maxlength' => '255',
            'required'  => true,
        ]);

        $fieldset->addField('detail', 'textarea', [
            'name'      => 'detail',
            'title'     => Mage::helper('review')->__('Review'),
            'label'     => Mage::helper('review')->__('Review'),
            'style'     => 'height: 600px;',
            'required'  => true,
        ]);

        $fieldset->addField('product_id', 'hidden', [
            'name'      => 'product_id',
        ]);

        /*$gridFieldset = $form->addFieldset('add_review_grid', array('legend' => Mage::helper('review')->__('Please select a product')));
        $gridFieldset->addField('products_grid', 'note', array(
            'text' => $this->getLayout()->createBlock('adminhtml/review_product_grid')->toHtml(),
        ));*/

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/post'));

        $this->setForm($form);
        return $this;
    }
}
