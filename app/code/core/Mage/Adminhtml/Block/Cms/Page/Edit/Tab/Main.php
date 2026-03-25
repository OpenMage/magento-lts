<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Cms page edit form main tab
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @inheritDoc
     */
    protected function _prepareForm()
    {
        /** @var Mage_Cms_Model_Page $model */
        $model = Mage::registry('cms_page');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('cms')->__('Page Information')]);

        if ($model->getPageId()) {
            $fieldset->addField('page_id', 'hidden', [
                'name' => 'page_id',
            ]);
        }

        $fieldset->addField('title', 'text', [
            'name'      => 'title',
            'label'     => Mage::helper('cms')->__('Page Title'),
            'title'     => Mage::helper('cms')->__('Page Title'),
            'required'  => true,
            'disabled'  => $isElementDisabled,
        ]);

        $fieldset->addField('identifier', 'text', [
            'name'      => 'identifier',
            'label'     => Mage::helper('cms')->__('URL Key'),
            'title'     => Mage::helper('cms')->__('URL Key'),
            'required'  => true,
            'class'     => 'validate-identifier',
            'note'      => Mage::helper('cms')->__('Relative to Website Base URL'),
            'disabled'  => $isElementDisabled,
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
                'disabled'  => $isElementDisabled,
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
            'title'     => Mage::helper('cms')->__('Page Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => $model->getAvailableStatuses(),
            'disabled'  => $isElementDisabled,
        ]);
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        Mage::dispatchEvent('adminhtml_cms_page_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('cms')->__('Page Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('cms')->__('Page Information');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param  string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
}
