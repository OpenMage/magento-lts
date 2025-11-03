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
class Mage_Adminhtml_Block_Catalog_Search_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init Form properties
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_search_form');
        $this->setTitle(Mage::helper('catalog')->__('Search Information'));
    }

    /**
     * Prepare form fields
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_catalog_search');
        /** @var Mage_CatalogSearch_Model_Query $model */

        $form = new Varien_Data_Form([
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method' => 'post',
        ]);

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('catalog')->__('General Information')]);

        $yesno = [
            [
                'value' => 0,
                'label' => Mage::helper('catalog')->__('No'),
            ],
            [
                'value' => 1,
                'label' => Mage::helper('catalog')->__('Yes'),
            ]];

        if ($model->getId()) {
            $fieldset->addField('query_id', 'hidden', [
                'name'      => 'query_id',
            ]);
        }

        $fieldset->addField('query_text', 'text', [
            'name'      => 'query_text',
            'label'     => Mage::helper('catalog')->__('Search Query'),
            'title'     => Mage::helper('catalog')->__('Search Query'),
            'required'  => true,
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', [
                'name'      => 'store_id',
                'label'     => Mage::helper('catalog')->__('Store'),
                'title'     => Mage::helper('catalog')->__('Store'),
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(true, false),
                'required'  => true,
            ]);

            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            if ($renderer instanceof Varien_Data_Form_Element_Renderer_Interface) {
                $field->setRenderer($renderer);
            }
        } else {
            $fieldset->addField('store_id', 'hidden', [
                'name'      => 'store_id',
            ]);
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        if ($model->getId()) {
            $fieldset->addField('num_results', 'text', [
                'name'     => 'num_results',
                'label'    => Mage::helper('catalog')->__('Number of results'),
                'title'    => Mage::helper('catalog')->__('Number of results (For the last time placed)'),
                'note'     => Mage::helper('catalog')->__('For the last time placed.'),
                'required' => true,
            ]);

            $fieldset->addField('popularity', 'text', [
                'name'     => 'popularity',
                'label'    => Mage::helper('catalog')->__('Number of Uses'),
                'title'    => Mage::helper('catalog')->__('Number of Uses'),
                'required' => true,
            ]);
        }

        $fieldset->addField('synonym_for', 'text', [
            'name'  => 'synonym_for',
            'label' => Mage::helper('catalog')->__('Synonym For'),
            'title' => Mage::helper('catalog')->__('Synonym For'),
            'note'  => Mage::helper('catalog')->__('Will make search for the query above return results for this search.'),
        ]);

        $fieldset->addField('redirect', 'text', [
            'name'  => 'redirect',
            'label' => Mage::helper('catalog')->__('Redirect URL'),
            'title' => Mage::helper('catalog')->__('Redirect URL'),
            'class' => 'validate-url',
            'note'  => Mage::helper('catalog')->__('ex. http://domain.com'),
        ]);

        $fieldset->addField('display_in_terms', 'select', [
            'name'   => 'display_in_terms',
            'label'  => Mage::helper('catalog')->__('Display in Suggested Terms'),
            'title'  => Mage::helper('catalog')->__('Display in Suggested Terms'),
            'values' => $yesno,
        ]);

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
