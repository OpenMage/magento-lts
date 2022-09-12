<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category edit general tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Tab_General extends Mage_Adminhtml_Block_Catalog_Form
{
    protected $_category;

    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    public function getCategory()
    {
        if (!$this->_category) {
            $this->_category = Mage::registry('category');
        }
        return $this->_category;
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_general');
        $form->setDataObject($this->getCategory());

        $fieldset = $form->addFieldset('base_fieldset', ['legend'=>Mage::helper('catalog')->__('General Information')]);

        if (!$this->getCategory()->getId()) {
            $parentId = $this->getRequest()->getParam('parent');
            if (!$parentId) {
                $parentId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
            }
            $fieldset->addField('path', 'hidden', [
                'name'  => 'path',
                'value' => $parentId
            ]);
        } else {
            $fieldset->addField('id', 'hidden', [
                'name'  => 'id',
                'value' => $this->getCategory()->getId()
            ]);
            $fieldset->addField('path', 'hidden', [
                'name'  => 'path',
                'value' => $this->getCategory()->getPath()
            ]);
        }

        $this->_setFieldset($this->getCategory()->getAttributes(true), $fieldset);

        if ($this->getCategory()->getId()) {
            if ($this->getCategory()->getLevel() == 1) {
                $fieldset->removeField('url_key');
                $fieldset->addField('url_key', 'hidden', [
                    'name'  => 'url_key',
                    'value' => $this->getCategory()->getUrlKey()
                ]);
            }
        }

        $form->addValues($this->getCategory()->getData());

        $form->setFieldNameSuffix('general');
        $this->setForm($form);
        return $this;
    }

    protected function _getAdditionalElementTypes()
    {
        return [
            'image' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_category_helper_image')
        ];
    }

    protected function _getParentCategoryOptions($node=null, &$options= [])
    {
        if (is_null($node)) {
            $node = $this->getRoot();
        }

        if ($node) {
            $options[] = [
               'value' => $node->getPathId(),
               'label' => str_repeat('&nbsp;', max(0, 3*($node->getLevel()))) . $this->escapeHtml($node->getName()),
            ];

            foreach ($node->getChildren() as $child) {
                $this->_getParentCategoryOptions($child, $options);
            }
        }
        return $options;
    }
}

