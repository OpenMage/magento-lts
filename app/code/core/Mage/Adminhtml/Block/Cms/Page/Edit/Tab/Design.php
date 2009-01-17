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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    public function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $model = Mage::registry('cms_page');

        $fieldset = $form->addFieldset('design_fieldset', array(
            'legend' => Mage::helper('cms')->__('Custom Design'),
            'class'  => 'fieldset-wide',
        ));

        $fieldset->addField('custom_theme', 'select', array(
            'name'      => 'custom_theme',
            'label'     => Mage::helper('cms')->__('Custom Theme'),
            'values'    => Mage::getModel('core/design_source_design')->getAllOptions(),
        ));

        $fieldset->addField('custom_theme_from', 'date', array(
            'name'      => 'custom_theme_from',
            'label'     => Mage::helper('cms')->__('Custom Theme From'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
        ));

        $fieldset->addField('custom_theme_to', 'date', array(
            'name'      => 'custom_theme_to',
            'label'     => Mage::helper('cms')->__('Custom Theme To'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
        ));

        $layouts = array();
        foreach (Mage::getConfig()->getNode('global/cms/layouts')->children() as $layoutName=>$layoutConfig) {
            $layouts[$layoutName] = (string)$layoutConfig->label;
        }
        $fieldset->addField('root_template', 'select', array(
            'name'      => 'root_template',
            'label'     => Mage::helper('cms')->__('Layout'),
            'required'  => true,
            'options'   => $layouts,
        ));

        $fieldset->addField('layout_update_xml', 'editor', array(
            'name'      => 'layout_update_xml',
            'label'     => Mage::helper('cms')->__('Layout Update XML'),
            'style'     => 'height:24em;'
        ));

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

}