<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml Review Edit Form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Review_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $review = Mage::registry('review_data');
        $product = Mage::getModel('catalog/product')->load($review->getEntityPkValue());
        $customer = Mage::getModel('customer/customer')->load($review->getCustomerId());

        $form = new Varien_Data_Form([
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id'), 'ret' => Mage::registry('ret')]),
            'method'    => 'post',
        ]);

        $fieldset = $form->addFieldset('review_details', ['legend' => Mage::helper('review')->__('Review Details'), 'class' => 'fieldset-wide']);

        $fieldset->addField('product_name', 'note', [
            'label'     => Mage::helper('review')->__('Product'),
            'text'      => '<a href="' . $this->getUrl('*/catalog_product/edit', ['id' => $product->getId()]) . '" onclick="this.target=\'blank\'">' . $this->escapeHtml($product->getName()) . '</a>',
        ]);

        $customerText = '';
        if ($customer->getId()) {
            $customerText = Mage::helper('review')->__('<a href="%1$s" onclick="this.target=\'blank\'">%2$s</a> <a href="mailto:%3$s">(%3$s)</a>', $this->getUrl('*/customer/edit', ['id' => $customer->getId(), 'active_tab' => 'review']), $this->escapeHtml($customer->getName()), $this->escapeHtml($customer->getEmail()));
        } elseif (is_null($review->getCustomerId())) {
            $customerText = Mage::helper('review')->__('Guest');
        } elseif ($review->getCustomerId() == 0) {
            $customerText = Mage::helper('review')->__('Administrator');
        }

        $fieldset->addField('customer', 'note', [
            'label'     => Mage::helper('review')->__('Posted By'),
            'text'      => $customerText,
        ]);

        $fieldset->addField('summary_rating', 'note', [
            'label'     => Mage::helper('review')->__('Summary Rating'),
            'text'      => $this->getLayout()->createBlock('adminhtml/review_rating_summary')->toHtml(),
        ]);

        $fieldset->addField('detailed_rating', 'note', [
            'label'     => Mage::helper('review')->__('Detailed Rating'),
            'required'  => true,
            'text'      => '<div id="rating_detail">'
                           . $this->getLayout()->createBlock('adminhtml/review_rating_detailed')->toHtml()
                           . '</div>',
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
                'name'      => 'stores[]',
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
            ]);
            $renderer = $this->getStoreSwitcherRenderer();
            $field->setRenderer($renderer);
            $review->setSelectStores($review->getStores());
        } else {
            $fieldset->addField('select_stores', 'hidden', [
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId(),
            ]);
            $review->setSelectStores(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('nickname', 'text', [
            'label'     => Mage::helper('review')->__('Nickname'),
            'required'  => true,
            'name'      => 'nickname',
        ]);

        $fieldset->addField('title', 'text', [
            'label'     => Mage::helper('review')->__('Summary of Review'),
            'required'  => true,
            'name'      => 'title',
        ]);

        $fieldset->addField('detail', 'textarea', [
            'label'     => Mage::helper('review')->__('Review'),
            'required'  => true,
            'name'      => 'detail',
            'style'     => 'height:24em;',
        ]);

        $form->setUseContainer(true);
        $form->setValues($review->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
