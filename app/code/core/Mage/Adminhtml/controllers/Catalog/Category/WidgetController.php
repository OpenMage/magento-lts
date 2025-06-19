<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Catalog category widgets controller for CMS WYSIWYG
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Catalog_Category_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'cms/widget_instance';

    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $this->getResponse()->setBody(
            $this->_getCategoryTreeBlock()->toHtml(),
        );
    }

    /**
     * Categories tree node (Ajax version)
     */
    public function categoriesJsonAction()
    {
        if ($categoryId = (int) $this->getRequest()->getPost('id')) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            if ($category->getId()) {
                Mage::register('category', $category);
                Mage::register('current_category', $category);
            }
            $this->getResponse()->setBody(
                $this->_getCategoryTreeBlock()->getTreeJson($category),
            );
        }
    }

    protected function _getCategoryTreeBlock()
    {
        return $this->getLayout()->createBlock('adminhtml/catalog_category_widget_chooser', '', [
            'id' => $this->getRequest()->getParam('uniq_id'),
            'use_massaction' => $this->getRequest()->getParam('use_massaction', false),
        ]);
    }
}
