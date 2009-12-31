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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Optimizer Cms Page Tab
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Googleoptimizer_Block_Adminhtml_Cms_Page_Edit_Tab_Googleoptimizer
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('googleoptimizer_fields',
            array('legend'=>Mage::helper('googleoptimizer')->__('Google Optimizer Scripts'))
        );

        Mage::helper('googleoptimizer')->setStoreId(Mage::app()->getDefaultStoreView());

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $fieldset->addField('conversion_page', 'select',
            array(
                'name'  => 'conversion_page',
                'label' => Mage::helper('googleoptimizer')->__('Conversion Page'),
                'values'=> Mage::getModel('googleoptimizer/adminhtml_system_config_source_googleoptimizer_conversionpages')->toOptionArray(),
                'class' => 'select googleoptimizer validate-googleoptimizer',
                'required' => false,
                'onchange' => 'googleOptimizerConversionPageAction(this)',
                'disabled'  => $isElementDisabled
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $form->getElement('conversion_page')->setOnchange('googleOptimizerConversionCmsPageAction(this)');
            $fieldset->addField('conversion_page_url', 'note',
                array(
                    'name'  => 'conversion_page_url',
                    'label' => Mage::helper('googleoptimizer')->__('Conversion Page URL'),
                    'disabled'  => $isElementDisabled
                )
            )->setRenderer($this->getLayout()->createBlock('googleoptimizer/adminhtml_cms_page_edit_renderer_conversion'));
        } else {
            $fieldset->addField('conversion_page_url', 'text',
                array(
                    'name'  => 'conversion_page_url',
                    'label' => Mage::helper('googleoptimizer')->__('Conversion Page URL'),
                    'class' => 'input-text',
                    'readonly' => 'readonly',
                    'required' => false,
                    'note' => Mage::helper('googleoptimizer')->__('Please copy and paste this value to experiment edit form'),
                    'disabled'  => $isElementDisabled
                )
            );
        }

        $fieldset->addField('export_controls', 'text',
            array(
                'name'  => 'export_controls',
                'disabled'  => $isElementDisabled
            )
        );

        $pageTypes = array(
            '' => Mage::helper('googleoptimizer')->__('-- Please Select --'),
            'original' => Mage::helper('googleoptimizer')->__('Original Page'),
            'variant' => Mage::helper('googleoptimizer')->__('Variant Page')
        );

        $fieldset->addField('page_type', 'select',
            array(
                'name'  => 'page_type',
                'label' => Mage::helper('googleoptimizer')->__('Page Type'),
                'values'=> $pageTypes,
                'class' => 'select googleoptimizer validate-googleoptimizer',
                'required' => false,
                'onchange' => 'googleOptimizerVariantPageAction(this)',
                'disabled'  => $isElementDisabled
            )
        );

        $fieldset->addField('control_script', 'textarea',
            array(
                'name'  => 'control_script',
                'label' => Mage::helper('googleoptimizer')->__('Control Script'),
                'class' => 'textarea validate-googleoptimizer',
                'required' => false,
                'note' => '',
                'disabled'  => $isElementDisabled
            )
        );
        $fieldset->addField('tracking_script', 'textarea',
            array(
                'name'  => 'tracking_script',
                'label' => Mage::helper('googleoptimizer')->__('Tracking Script'),
                'class' => 'textarea validate-googleoptimizer',
                'required' => false,
                'note' => '',
                'disabled'  => $isElementDisabled
            )
        );
        $fieldset->addField('conversion_script', 'textarea',
            array(
                'name'  => 'conversion_script',
                'label' => Mage::helper('googleoptimizer')->__('Conversion Script'),
                'class' => 'textarea validate-googleoptimizer',
                'required' => false,
                'note' => '',
                'disabled'  => $isElementDisabled
            )
        );

        if (Mage::helper('googleoptimizer')->getConversionPagesUrl()
            && $this->getGoogleOptimizer()
            && $this->getGoogleOptimizer()->getConversionPage())
        {
            $form->getElement('conversion_page_url')
                ->setValue(Mage::helper('googleoptimizer')
                    ->getConversionPagesUrl()->getData($this->getGoogleOptimizer()->getConversionPage())
                );
        }

        $renderer = $this->getLayout()->createBlock('adminhtml/catalog_form_renderer_googleoptimizer_import');
        $form->getElement('export_controls')->setRenderer($renderer);

        $values = array();
        if ($this->getGoogleOptimizer() && $this->getGoogleOptimizer()->getData()) {
            $values = $this->getGoogleOptimizer()->getData();
            $fieldset->addField('code_id', 'hidden', array('name' => 'code_id'));
            if ($this->getGoogleOptimizer()->getData('page_type') == Mage_Googleoptimizer_Model_Code_Page::PAGE_TYPE_VARIANT) {
                foreach ($fieldset->getElements() as $element) {
                    if (($element->getId() != 'tracking_script' && $element->getId() != 'page_type')
                        && ($element->getType() == 'textarea' || $element->getType() == 'select'))
                    {
                        $element->setDisabled(true);
                    }
                }
            }
        }

        $form->addValues($values);
        $form->setFieldNameSuffix('googleoptimizer');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getCmsPage()
    {
        return Mage::registry('cms_page');
    }

    public function getGoogleOptimizer()
    {
        if ($this->getCmsPage()->getGoogleoptimizer()) {//if data was set from session after exception
            $googleOptimizer = new Varien_Object($this->getCmsPage()->getGoogleoptimizer());
        } else {
            $googleOptimizer = $this->getCmsPage()->getGoogleOptimizerScripts();
        }
        return $googleOptimizer;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('googleoptimizer')->__('Page View Optimization');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('googleoptimizer')->__('Page View Optimization');
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
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /** Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
}
