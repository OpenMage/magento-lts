<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

use Mage_Cms_Api_Data_PageInterface as PageInterface;

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _prepareForm()
    {
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

        $model = Mage::registry('cms_page');

        $layoutFieldset = $form->addFieldset('layout_fieldset', [
            'legend' => Mage::helper('cms')->__('Page Layout'),
            'class'  => 'fieldset-wide',
            'disabled'  => $isElementDisabled,
        ]);

        $layoutFieldset->addField(PageInterface::DATA_ROOT_TEMPLATE, 'select', [
            'name'     => PageInterface::DATA_ROOT_TEMPLATE,
            'label'    => Mage::helper('cms')->__('Layout'),
            'required' => true,
            'values'   => Mage::getSingleton('page/source_layout')->toOptionArray(),
            'disabled' => $isElementDisabled,
        ]);
        if (!$model->getId()) {
            $model->setRootTemplate(Mage::getSingleton('page/source_layout')->getDefaultValue());
        }

        $layoutFieldset->addField(PageInterface::DATA_LAYOUT_UPDATE_XML, 'textarea', [
            'name'      => PageInterface::DATA_LAYOUT_UPDATE_XML,
            'label'     => Mage::helper('cms')->__('Layout Update XML'),
            'style'     => 'height:24em;',
            'disabled'  => $isElementDisabled,
        ]);

        $designFieldset = $form->addFieldset('design_fieldset', [
            'legend' => Mage::helper('cms')->__('Custom Design'),
            'class'  => 'fieldset-wide',
            'disabled'  => $isElementDisabled,
        ]);

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT,
        );

        $designFieldset->addField(PageInterface::DATA_CUSTOM_THEME_FROM, 'date', [
            'name'      => PageInterface::DATA_CUSTOM_THEME_FROM,
            'label'     => Mage::helper('cms')->__('Custom Design From'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $dateFormatIso,
            'disabled'  => $isElementDisabled,
            'class'     => 'validate-date validate-date-range date-range-custom_theme-from',
        ]);

        $designFieldset->addField(PageInterface::DATA_CUSTOM_THEME_TO, 'date', [
            'name'      => PageInterface::DATA_CUSTOM_THEME_TO,
            'label'     => Mage::helper('cms')->__('Custom Design To'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $dateFormatIso,
            'disabled'  => $isElementDisabled,
            'class'     => 'validate-date validate-date-range date-range-custom_theme-to',
        ]);

        $designFieldset->addField(PageInterface::DATA_CUSTOM_THEME, 'select', [
            'name'      => PageInterface::DATA_CUSTOM_THEME,
            'label'     => Mage::helper('cms')->__('Custom Theme'),
            'values'    => Mage::getModel('core/design_source_design')->getAllOptions(),
            'disabled'  => $isElementDisabled,
        ]);

        $designFieldset->addField(PageInterface::DATA_CUSTOM_ROOT_TEMPLATE, 'select', [
            'name'      => PageInterface::DATA_CUSTOM_ROOT_TEMPLATE,
            'label'     => Mage::helper('cms')->__('Custom Layout'),
            'values'    => Mage::getSingleton('page/source_layout')->toOptionArray(true),
            'disabled'  => $isElementDisabled,
        ]);

        $designFieldset->addField(PageInterface::DATA_CUSTOM_LAYOUT_UPDATE_XML, 'textarea', [
            'name'      => PageInterface::DATA_CUSTOM_LAYOUT_UPDATE_XML,
            'label'     => Mage::helper('cms')->__('Custom Layout Update XML'),
            'style'     => 'height:24em;',
            'disabled'  => $isElementDisabled,
        ]);

        Mage::dispatchEvent('adminhtml_cms_page_edit_tab_design_prepare_form', ['form' => $form]);

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
        return Mage::helper('cms')->__('Design');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('cms')->__('Design');
    }

    /**
     * Returns status flag about this tab can be showen or not
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
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
}
