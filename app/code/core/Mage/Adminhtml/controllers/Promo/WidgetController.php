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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Adminhtml_Promo_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Prepare block for chooser
     *
     * @return void
     */
    public function chooserAction()
    {
        $request = $this->getRequest();

        switch ($request->getParam('attribute')) {
            case 'sku':
                $block = $this->getLayout()->createBlock(
                    'adminhtml/promo_widget_chooser_sku', 'promo_widget_chooser_sku',
                    array('js_form_object' => $request->getParam('form'),
                ));
                break;

            case 'category_ids':
                $ids = $request->getParam('selected', array());
                if (is_array($ids)) {
                    foreach ($ids as $key => &$id) {
                        $id = (int) $id;
                        if ($id <= 0) {
                            unset($ids[$key]);
                        }
                    }

                    $ids = array_unique($ids);
                } else {
                    $ids = array();
                }


                $block = $this->getLayout()->createBlock(
                        'adminhtml/catalog_category_checkboxes_tree', 'promo_widget_chooser_category_ids',
                        array('js_form_object' => $request->getParam('form'))
                    )
                    ->setCategoryIds($ids)
                ;
                break;

            default:
                $block = false;
                break;
        }

        if ($block) {
            $this->getResponse()->setBody($block->toHtml());
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/catalog');
    }

    /**
     * Get tree node (Ajax version)
     */
    public function categoriesJsonAction()
    {
        if ($categoryId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $categoryId);

            if (!$category = $this->_initCategory()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('adminhtml/catalog_category_tree')
                    ->getTreeJson($category)
            );
        }
    }

    /**
     * Initialize category object in registry
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _initCategory()
    {
        $categoryId = (int) $this->getRequest()->getParam('id',false);
        $storeId    = (int) $this->getRequest()->getParam('store');

        $category   = Mage::getModel('catalog/category');
        $category->setStoreId($storeId);

        if ($categoryId) {
            $category->load($categoryId);
            if ($storeId) {
                $rootId = Mage::app()->getStore($storeId)->getRootCategoryId();
                if (!in_array($rootId, $category->getPathIds())) {
                    $this->_redirect('*/*/', array('_current'=>true, 'id'=>null));
                    return false;
                }
            }
        }

        Mage::register('category', $category);
        Mage::register('current_category', $category);

        return $category;
    }
}
