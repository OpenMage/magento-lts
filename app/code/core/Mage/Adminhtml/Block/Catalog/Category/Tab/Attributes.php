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
 * Adminhtml Catalog Category Attributes per Group Tab block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Form
{
    /**
     * Retrieve Category object
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * Initialize tab
     *
     */
    public function __construct() {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm() {
        $group      = $this->getGroup();
        $attributes = $this->getAttributes();

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('group_' . $group->getId());
        $form->setDataObject($this->getCategory());

        $fieldset = $form->addFieldset('fieldset_group_' . $group->getId(), array(
            'legend'    => Mage::helper('catalog')->__($group->getAttributeGroupName()),
            'class'     => 'fieldset-wide',
        ));

        if ($this->getAddHiddenFields()) {
            if (!$this->getCategory()->getId()) {
                // path
                if ($this->getRequest()->getParam('parent')) {
                    $fieldset->addField('path', 'hidden', array(
                        'name'  => 'path',
                        'value' => $this->getRequest()->getParam('parent')
                    ));
                }
                else {
                    $fieldset->addField('path', 'hidden', array(
                        'name'  => 'path',
                        'value' => 1
                    ));
                }
            }
            else {
                $fieldset->addField('id', 'hidden', array(
                    'name'  => 'id',
                    'value' => $this->getCategory()->getId()
                ));
                $fieldset->addField('path', 'hidden', array(
                    'name'  => 'path',
                    'value' => $this->getCategory()->getPath()
                ));
            }
        }

        $this->_setFieldset($attributes, $fieldset);
        foreach ($attributes as $attribute) {
            $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if ($attribute->getAttributeCode() == 'url_key') {
                if (
                    (!$this->getCategory()->getId() && $this->getRequest()->getParam('parent', $rootId) == $rootId)
                    || ($this->getCategory()->getParentId() == $rootId)
                ) {
                    $fieldset->removeField('url_key');
                } else {
                    $form->getElement('url_key')->setRenderer(
                        $this->getLayout()->createBlock('adminhtml/catalog_form_renderer_attribute_urlkey')
                    );
                }
            }
        }

        if ($this->getCategory()->getLevel() == 1) {
            $fieldset->removeField('custom_use_parent_settings');
        } else {
            if ($this->getCategory()->getCustomUseParentSettings()) {
                foreach ($this->getCategory()->getDesignAttributes() as $attribute) {
                    if ($element = $form->getElement($attribute->getAttributeCode())) {
                        $element->setDisabled(true);
                    }
                }
            }
            if ($element = $form->getElement('custom_use_parent_settings')) {
                $element->setData('onchange', 'onCustomUseParentChanged(this)');
            }
        }

        if ($this->getCategory()->hasLockedAttributes()) {
            foreach ($this->getCategory()->getLockedAttributes() as $attribute) {
                if ($element = $form->getElement($attribute)) {
                    $element->setReadonly(true, true);
                }
            }
        }

        if (!$this->getCategory()->getId()){
            $this->getCategory()->setIncludeInMenu(1);
        }

        $form->addValues($this->getCategory()->getData());

        Mage::dispatchEvent('adminhtml_catalog_category_edit_prepare_form', array('form'=>$form));

        $form->setFieldNameSuffix('general');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve Additional Element Types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_category_helper_image'),
            'textarea' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_helper_form_wysiwyg')
        );
    }
}
