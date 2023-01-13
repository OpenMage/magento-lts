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
 * Sitemap edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sitemap_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('sitemap_form');
        $this->setTitle(Mage::helper('adminhtml')->__('Sitemap Information'));
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('sitemap_sitemap');

        $form = new Varien_Data_Form([
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ]);

        $fieldset = $form->addFieldset('add_sitemap_form', ['legend' => Mage::helper('sitemap')->__('Sitemap')]);

        if ($model->getId()) {
            $fieldset->addField('sitemap_id', 'hidden', [
                'name' => 'sitemap_id',
            ]);
        }

        $fieldset->addField('sitemap_filename', 'text', [
            'label' => Mage::helper('sitemap')->__('Filename'),
            'name'  => 'sitemap_filename',
            'required' => true,
            'note'  => Mage::helper('adminhtml')->__('example: sitemap.xml'),
            'value' => $model->getSitemapFilename()
        ]);

        $fieldset->addField('sitemap_path', 'text', [
            'label' => Mage::helper('sitemap')->__('Path'),
            'name'  => 'sitemap_path',
            'required' => true,
            'note'  => Mage::helper('adminhtml')->__('example: "sitemap/" or "/" for base path (path must be writeable)'),
            'value' => $model->getSitemapPath()
        ]);

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', [
                'label'    => Mage::helper('sitemap')->__('Store View'),
                'title'    => Mage::helper('sitemap')->__('Store View'),
                'name'     => 'store_id',
                'required' => true,
                'value'    => $model->getStoreId(),
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
            ]);
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', [
                'name'     => 'store_id',
                'value'    => Mage::app()->getStore(true)->getId()
            ]);
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('generate', 'hidden', [
            'name'     => 'generate',
            'value'    => ''
        ]);

        $form->setValues($model->getData());

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
