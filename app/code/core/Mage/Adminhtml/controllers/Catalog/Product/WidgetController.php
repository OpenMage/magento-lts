<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Catalog Product widgets controller for CMS WYSIWYG
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Catalog_Product_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'cms/widget_instance';

    /**
     * Chooser Source action
     *
     * @throws Mage_Core_Exception
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $massAction = $this->getRequest()->getParam('use_massaction', false);
        $productTypeId = $this->getRequest()->getParam('product_type_id', null);

        if (!$this->_validateRequestParam($uniqId)) {
            Mage::throwException(Mage::helper('adminhtml')->__('An error occurred while adding condition.'));
        }

        $productsGrid = $this->getLayout()->createBlock('adminhtml/catalog_product_widget_chooser', '', [
            'id'                => $uniqId,
            'use_massaction' => $massAction,
            'product_type_id' => $productTypeId,
            'category_id'       => $this->getRequest()->getParam('category_id'),
        ]);

        $html = $productsGrid->toHtml();

        if (!$this->getRequest()->getParam('products_grid')) {
            $categoriesTree = $this->getLayout()->createBlock('adminhtml/catalog_category_widget_chooser', '', [
                'id'                  => $uniqId . 'Tree',
                'node_click_listener' => $productsGrid->getCategoryClickListenerJs(),
                'with_empty_node'     => true,
            ]);

            $html = $this->getLayout()->createBlock('adminhtml/catalog_product_widget_chooser_container')
                ->setTreeHtml($categoriesTree->toHtml())
                ->setGridHtml($html)
                ->toHtml();
        }

        $this->getResponse()->setBody($html);
    }
}
