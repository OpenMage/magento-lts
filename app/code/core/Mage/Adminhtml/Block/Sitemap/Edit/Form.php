<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Sitemap edit form
 *
 * @package    Mage_Adminhtml
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
            'method'    => 'post',
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
            'value' => $model->getSitemapFilename(),
        ]);

        $fieldset->addField('sitemap_path', 'text', [
            'label' => Mage::helper('sitemap')->__('Path'),
            'name'  => 'sitemap_path',
            'required' => true,
            'note'  => Mage::helper('adminhtml')->__('example: "sitemap/" or "/" for base path (path must be writeable)'),
            'value' => $model->getSitemapPath(),
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
            $renderer = $this->getStoreSwitcherRenderer();
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', [
                'name'     => 'store_id',
                'value'    => Mage::app()->getStore(true)->getId(),
            ]);
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('generate', 'hidden', [
            'name'     => 'generate',
            'value'    => '',
        ]);

        $form->setValues($model->getData());

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
