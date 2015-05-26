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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block for Urlrewrites edit form and selectors container
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Urlrewrite_Edit extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Part for building some blocks names
     *
     * @var string
     */
    protected $_controller = 'urlrewrite';

    /**
     * Generated buttons html cache
     *
     * @var string
     */
    protected $_buttonsHtml;

    /**
     * Prepare page layout, basing on different registry and request variables
     *
     * Generates layout of: creation modes selector, products grid, categories tree, urlrewrite edit form
     * @return Mage_Adminhtml_Block_Urlrewrite_Edit
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('urlrewrite/edit.phtml');
        $this->_addButton('back', array(
            'label'   => Mage::helper('adminhtml')->__('Back'),
            'onclick' => 'setLocation(\'' . Mage::helper('adminhtml')->getUrl('*/*/') . '\')',
            'class'   => 'back',
            'level'   => -1
        ));

        // links to products/categories (if any) selectors
        if ($this->getProductId()) {
            $this->setChild('product_link', $this->getLayout()->createBlock('adminhtml/urlrewrite_link')
                ->setData(array(
                    'item_url' => Mage::helper('adminhtml')->getUrl('*/*/*') . 'product',
                    'item'     => Mage::registry('current_product'),
                    'label'    => Mage::helper('adminhtml')->__('Product:')
                ))
            );
        }
        if ($this->getCategoryId()) {
            $itemUrl = Mage::helper('adminhtml')->getUrl('*/*/*') . 'category';
            if ($this->getProductId()) {
                $itemUrl = Mage::helper('adminhtml')->getUrl('*/*/*', array('product' => $this->getProductId())) . 'category';
            }
            $this->setChild('category_link', $this->getLayout()->createBlock('adminhtml/urlrewrite_link')
                ->setData(array(
                    'item_url' => $itemUrl,
                    'item'     => Mage::registry('current_category'),
                    'label'    => Mage::helper('adminhtml')->__('Category:')
                ))
            );
        }

        $this->_headerText = Mage::helper('adminhtml')->__('Add New URL Rewrite');

        // edit form for existing urlrewrite
        if ($this->getUrlrewriteId()) {
            $this->_headerText = Mage::helper('adminhtml')->__('Edit URL Rewrite');
            $this->_setFormChild();
        }
        elseif ($this->getProductId()) {
            $this->_headerText = Mage::helper('adminhtml')->__('Add URL Rewrite for a Product');

            // edit form for product with or without category
            if ($this->getCategoryId() || !$this->isMode('category')) {
                $this->_setFormChild();
            }
            // categories selector & skip categories button
            else {
                $this->setChild('categories_tree', $this->getLayout()->createBlock('adminhtml/urlrewrite_category_tree'));
                $this->setChild('skip_categories',
                    $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                        'label'   => Mage::helper('adminhtml')->__('Skip Category Selection'),
                        'onclick' => 'window.location = \'' . Mage::helper('adminhtml')->getUrl('*/*/*', array(
                            'product' => $this->getProductId()
                        )) . '\'',
                        'class'   => 'save',
                        'level'   => -1
                    ))
                );
                $this->_updateButton('back', 'onclick', 'setLocation(\'' . Mage::helper('adminhtml')->getUrl('*/*/edit') . 'product\')');
            }
        }
        // edit form for category
        elseif ($this->getCategoryId()) {
            $this->_headerText = Mage::helper('adminhtml')->__('Add URL Rewrite for a Category');
            $this->_setFormChild();
        }
        // modes selector and products/categories selectors, as well as edit form for custom urlrewrite
        else {
            $this->setChild('selector', $this->getLayout()->createBlock('adminhtml/urlrewrite_selector'));

            if ($this->isMode('id')) {
                $this->updateModeLayout('id');
            }
            elseif ($this->isMode('product')) {
                $this->updateModeLayout('product');
            }
            elseif ($this->isMode('category')) {
                $this->updateModeLayout('category');
            }
            else {
                $this->updateModeLayout();
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Add edit form as child block and add appropriate buttons
     *
     * @return Mage_Adminhtml_Block_Urlrewrite_Edit
     */
    protected function _setFormChild()
    {
        $this->setChild('form', Mage::getBlockSingleton('adminhtml/urlrewrite_edit_form'));
        if ($this->getUrlrewriteId()) {
            $this->_addButton('reset', array(
                'label'   => Mage::helper('adminhtml')->__('Reset'),
                'onclick' => '$(\'edit_form\').reset()',
                'class'   => 'scalable',
                'level'   => -1
            ));
            $this->_addButton('delete', array(
                'label'   => Mage::helper('adminhtml')->__('Delete'),
                'onclick' => 'deleteConfirm(\'' . Mage::helper('adminhtml')->__('Are you sure you want to do this?')
                    . '\', \'' . Mage::helper('adminhtml')->getUrl('*/*/delete', array('id' => $this->getUrlrewriteId())) . '\')',
                'class'   => 'scalable delete',
                'level'   => -1
            ));
        }
        $this->_addButton('save', array(
            'label'   => Mage::helper('adminhtml')->__('Save'),
            'onclick' => 'editForm.submit()',
            'class'   => 'save',
            'level'   => -1
        ));

        // update back button link
        $params = array();
        $suffix = '';
        $action = '';
        if (!$this->getUrlrewriteId()) {
            $action = 'edit';
            if ($this->getProductId()) {
                $suffix = 'category';
                $params['product'] = $this->getProductId();
            }
            elseif ($this->getCategoryId()) {
                $suffix = 'category';
            }
        }
        $this->_updateButton('back', 'onclick', 'setLocation(\'' . Mage::helper('adminhtml')->getUrl('*/*/' . $action, $params) . $suffix . '\')');

        return $this;
    }

    /**
     * Get container buttons HTML
     *
     * Since buttons are set as children, we remove them as children after generating them
     * not to duplicate them in future
     *
     * @return string
     */
    public function getButtonsHtml($area = null)
    {
        if (null === $this->_buttonsHtml) {
            $this->_buttonsHtml = parent::getButtonsHtml();
            foreach ($this->_children as $alias => $child) {
                if (false !== strpos($alias, '_button')) {
                    $this->unsetChild($alias);
                }
            }
        }
        return $this->_buttonsHtml;
    }

    /**
     * Get current urlrewrite instance id
     *
     * @return int
     */
    public function getUrlrewriteId()
    {
        return Mage::registry('current_urlrewrite')->getId();
    }

    /**
     * Get current product instance id
     *
     * @return int
     */
    public function getProductId()
    {
        return Mage::registry('current_product')->getId();
    }

    /**
     * Return current category instance id
     *
     * @return int
     */
    public function getCategoryId()
    {
        return Mage::registry('current_category')->getId();
    }

    /**
     * Check whether specified selection mode is set in request
     *
     * @param string $mode
     * @return bool
     */
    public function isMode($mode)
    {
        return $this->getRequest()->has($mode);
    }

    /**
     * Update layout by specified mode code
     *
     * @param string $mode
     * @return Mage_Adminhtml_Block_Urlrewrite_Edit
     * @see Mage_Adminhtml_Block_Urlrewrite_Selector
     */
    public function updateModeLayout($mode = null)
    {
        if (!$mode) {
            $modes = array_keys(Mage::getBlockSingleton('adminhtml/urlrewrite_selector')->getModes());
            $mode  = array_shift($modes);
        }

        // edit form for new custom urlrewrite
        if ('id' === $mode) {
            $this->_setFormChild();
        }
        // products grid
        elseif ('product' === $mode) {
            $this->setChild('products_grid', $this->getLayout()->createBlock('adminhtml/urlrewrite_product_grid'));
        }
        // categories tree
        elseif ('category' === $mode) {
            $this->setChild('categories_tree', $this->getLayout()->createBlock('adminhtml/urlrewrite_category_tree'));
        }
        return $this;
    }
}
