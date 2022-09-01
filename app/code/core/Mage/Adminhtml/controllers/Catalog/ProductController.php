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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    const ADMIN_RESOURCE = 'catalog/products';

    /**
     * The greatest value which could be stored in CatalogInventory Qty field
     */
    const MAX_QTY_VALUE = 99999999.9999;

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = ['edit'];

    /**
     * Controller pre-dispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions(['delete', 'massDelete']);
        return parent::preDispatch();
    }

    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('Mage_Catalog');
    }

    /**
     * Initialize product from request parameters
     *
     * @return Mage_Catalog_Model_Product
     * @throws Mage_Core_Exception
     */
    protected function _initProduct()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Manage Products'));

        $productId  = (int) $this->getRequest()->getParam('id');
        $product    = Mage::getModel('catalog/product')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if (!$productId) {
            if ($setId = (int) $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }

            if ($typeId = $this->getRequest()->getParam('type')) {
                $product->setTypeId($typeId);
            }
        }

        $product->setData('_edit_mode', true);
        if ($productId) {
            try {
                $product->load($productId);
            } catch (Exception $e) {
                $product->setTypeId(Mage_Catalog_Model_Product_Type::DEFAULT_TYPE);
                Mage::logException($e);
            }
        }

        $attributes = $this->getRequest()->getParam('attributes');
        if ($attributes && $product->isConfigurable() &&
            (!$productId || !$product->getTypeInstance()->getUsedProductAttributeIds())) {
            $product->getTypeInstance()->setUsedProductAttributeIds(
                explode(",", base64_decode(urldecode($attributes)))
            );
        }

        // Required attributes of simple product for configurable creation
        if ($this->getRequest()->getParam('popup')
            && $requiredAttributes = $this->getRequest()->getParam('required')) {
            $requiredAttributes = explode(",", $requiredAttributes);
            foreach ($product->getAttributes() as $attribute) {
                if (in_array($attribute->getId(), $requiredAttributes)) {
                    $attribute->setIsRequired(1);
                }
            }
        }

        if ($this->getRequest()->getParam('popup')
            && $this->getRequest()->getParam('product')
            && !is_array($this->getRequest()->getParam('product'))
            && $this->getRequest()->getParam('id', false) === false) {

            $configProduct = Mage::getModel('catalog/product')
                ->setStoreId(0)
                ->load($this->getRequest()->getParam('product'))
                ->setTypeId($this->getRequest()->getParam('type'));

            /** @var Mage_Catalog_Model_Product $configProduct */
            $data = [];
            foreach ($configProduct->getTypeInstance()->getEditableAttributes() as $attribute) {

                /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
                if(!$attribute->getIsUnique()
                    && $attribute->getFrontend()->getInputType()!='gallery'
                    && $attribute->getAttributeCode() != 'required_options'
                    && $attribute->getAttributeCode() != 'has_options'
                    && $attribute->getAttributeCode() != $configProduct->getIdFieldName()) {
                    $data[$attribute->getAttributeCode()] = $configProduct->getData($attribute->getAttributeCode());
                }
            }

            $product->addData($data)
                ->setWebsiteIds($configProduct->getWebsiteIds());
        }

        Mage::register('product', $product);
        Mage::register('current_product', $product);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $product;
    }

    /**
     * Create serializer block for a grid
     *
     * @param string $inputName
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
     * @param array $productsArray
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Ajax_Serializer
     */
    protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray)
    {
        return $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_ajax_serializer')
            ->setGridBlock($gridBlock)
            ->setProducts($productsArray)
            ->setInputElementName($inputName)
        ;
    }

    /**
     * Output specified blocks as a text list
     */
    protected function _outputBlocks()
    {
        $blocks = func_get_args();
        $output = $this->getLayout()->createBlock('adminhtml/text_list');
        foreach ($blocks as $block) {
            $output->insert($block, '', true);
        }
        $this->getResponse()->setBody($output->toHtml());
    }

    /**
     * Product list page
     */
    public function indexAction()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Manage Products'));

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Create new product page
     */
    public function newAction()
    {
        $product = $this->_initProduct();

        $this->_title($this->__('New Product'));

        Mage::dispatchEvent('catalog_product_new_action', ['product' => $product]);

        if ($this->getRequest()->getParam('popup')) {
            $this->loadLayout('popup');
        } else {
            $_additionalLayoutPart = '';
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
                && !($product->getTypeInstance()->getUsedProductAttributeIds()))
            {
                $_additionalLayoutPart = '_new';
            }
            $this->loadLayout([
                'default',
                strtolower($this->getFullActionName()),
                'adminhtml_catalog_product_'.$product->getTypeId() . $_additionalLayoutPart
            ]);
            $this->_setActiveMenu('catalog/products');
        }

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }

        $this->renderLayout();
    }

    /**
     * Product edit form
     */
    public function editAction()
    {
        $productId  = (int) $this->getRequest()->getParam('id');
        $product = $this->_initProduct();

        if ($productId && !$product->getId()) {
            $this->_getSession()->addError(Mage::helper('catalog')->__('This product no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($product->getName());

        Mage::dispatchEvent('catalog_product_edit_action', ['product' => $product]);

        $_additionalLayoutPart = '';
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE
            && !($product->getTypeInstance()->getUsedProductAttributeIds()))
        {
            $_additionalLayoutPart = '_new';
        }

        $this->loadLayout([
            'default',
            strtolower($this->getFullActionName()),
            'adminhtml_catalog_product_'.$product->getTypeId() . $_additionalLayoutPart
        ]);

        $this->_setActiveMenu('catalog/products');

        if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
            $switchBlock->setDefaultStoreName($this->__('Default Values'))
                ->setWebsiteIds($product->getWebsiteIds())
                ->setSwitchUrl(
                    $this->getUrl('*/*/*', ['_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null])
                );
        }

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }

        $this->renderLayout();
    }

    /**
     * WYSIWYG editor action for ajax request
     *
     * @throws Mage_Core_Model_Store_Exception|Mage_Core_Exception
     */
    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock('adminhtml/catalog_helper_form_wysiwyg_content', '', [
            'editor_element_id' => $elementId,
            'store_id'          => $storeId,
            'store_media_url'   => $storeMediaUrl,
        ]);
        $this->getResponse()->setBody($content->toHtml());
    }

    /**
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Get specified tab grid
     * @throws Mage_Core_Exception
     */
    public function gridOnlyAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('adminhtml/catalog_product_edit_tab_' . $this->getRequest()->getParam('gridOnlyBlock'))
                ->toHtml()
        );
    }

    /**
     * Get categories fieldset block
     *
     * @throws Mage_Core_Exception
     */
    public function categoriesAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Get options fieldset block
     *
     * @throws Mage_Core_Exception
     */
    public function optionsAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Get related products grid and serializer block
     * @throws Mage_Core_Exception
     */
    public function relatedAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.related')
            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }

    /**
     * Get upsell products grid and serializer block
     * @throws Mage_Core_Exception
     */
    public function upsellAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.upsell')
            ->setProductsUpsell($this->getRequest()->getPost('products_upsell', null));
        $this->renderLayout();
    }

    /**
     * Get crosssell products grid and serializer block
     * @throws Mage_Core_Exception
     */
    public function crosssellAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.crosssell')
            ->setProductsCrossSell($this->getRequest()->getPost('products_crosssell', null));
        $this->renderLayout();
    }

    /**
     * Get related products grid
     * @throws Mage_Core_Exception
     */
    public function relatedGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.related')
            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }

    /**
     * Get upsell products grid
     * @throws Mage_Core_Exception
     */
    public function upsellGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.upsell')
            ->setProductsRelated($this->getRequest()->getPost('products_upsell', null));
        $this->renderLayout();
    }

    /**
     * Get crosssell products grid
     * @throws Mage_Core_Exception
     */
    public function crosssellGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.crosssell')
            ->setProductsRelated($this->getRequest()->getPost('products_crosssell', null));
        $this->renderLayout();
    }

    /**
     * Get associated grouped products grid and serializer block
     * @throws Mage_Core_Exception
     */
    public function superGroupAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.super.group')
            ->setProductsGrouped($this->getRequest()->getPost('products_grouped', null));
        $this->renderLayout();
    }

    /**
     * Get associated grouped products grid only
     *
     * @throws Mage_Core_Exception
     */
    public function superGroupGridOnlyAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.super.group')
            ->setProductsGrouped($this->getRequest()->getPost('products_grouped', null));
        $this->renderLayout();
    }

    /**
     * Get product reviews grid
     *
     * @throws Mage_Core_Exception
     */
    public function reviewsAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('admin.product.reviews')
                ->setProductId(Mage::registry('product')->getId())
                ->setUseAjax(true);
        $this->renderLayout();
    }

    /**
     * Get super config grid
     *
     * @throws Mage_Core_Exception
     */
    public function superConfigAction()
    {
        $this->_initProduct();
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * @throws Mage_Core_Exception
     * @deprecated since 1.2
     */
    public function bundlesAction()
    {
        $product = $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('bundle/adminhtml_catalog_product_edit_tab_bundle', 'admin.product.bundle.items')
                ->setProductId($product->getId())
                ->toHtml()
        );
    }

    /**
     * Validate product
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        try {
            $productData = $this->getRequest()->getPost('product');

            if ($productData && !isset($productData['stock_data']['use_config_manage_stock'])) {
                $productData['stock_data']['use_config_manage_stock'] = 0;
            }
            /** @var Mage_Catalog_Model_Product $product */
            $product = Mage::getModel('catalog/product');
            $product->setData('_edit_mode', true);
            if ($storeId = $this->getRequest()->getParam('store')) {
                $product->setStoreId($storeId);
            }
            if ($setId = $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }
            if ($typeId = $this->getRequest()->getParam('type')) {
                $product->setTypeId($typeId);
            }
            if ($productId = $this->getRequest()->getParam('id')) {
                $product->load($productId);
            }

            $dateFields = [];
            $attributes = $product->getAttributes();
            foreach ($attributes as $attrKey => $attribute) {
                if ($attribute->getBackend()->getType() == 'datetime') {
                    if (array_key_exists($attrKey, $productData) && $productData[$attrKey] != ''){
                        $dateFields[] = $attrKey;
                    }
                }
            }
            $productData = $this->_filterDates($productData, $dateFields);
            $product->addData($productData);

            /* set restrictions for date ranges */
            $resource = $product->getResource();
            $resource->getAttribute('special_from_date')
                ->setMaxValue($product->getSpecialToDate());
            $resource->getAttribute('news_from_date')
                ->setMaxValue($product->getNewsToDate());
            $resource->getAttribute('custom_design_from')
                ->setMaxValue($product->getCustomDesignTo());

            $product->validate();
        }
        catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $response->setError(true);
            $response->setAttribute($e->getAttributeCode());
            $response->setMessage(Mage::helper('core')->escapeHtml($e->getMessage()));
        } catch (Mage_Core_Exception $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Initialize product before saving
     * @throws Mage_Core_Exception|Zend_Json_Exception
     */
    protected function _initProductSave()
    {
        $product     = $this->_initProduct();
        $productData = $this->getRequest()->getPost('product');
        if ($productData) {
            $this->_filterStockData($productData['stock_data']);
        }

        /**
         * Websites
         */
        if (!isset($productData['website_ids'])) {
            $productData['website_ids'] = [];
        }

        $wasLockedMedia = false;
        if ($product->isLockedAttribute('media')) {
            $product->unlockAttribute('media');
            $wasLockedMedia = true;
        }

        $product->addData($productData);

        if ($wasLockedMedia) {
            $product->lockAttribute('media');
        }

        if (Mage::app()->isSingleStoreMode()) {
            $product->setWebsiteIds([Mage::app()->getStore(true)->getWebsite()->getId()]);
        }

        /**
         * Create Permanent Redirect for old URL key
         */
        if ($product->getId() && isset($productData['url_key_create_redirect'])) {
            $product->setData('save_rewrites_history', (bool)$productData['url_key_create_redirect']);
        }

        /**
         * Check "Use Default Value" checkboxes values
         */
        if ($useDefaults = $this->getRequest()->getPost('use_default')) {
            foreach ($useDefaults as $attributeCode) {
                $product->setData($attributeCode, false);
            }
        }

        /**
         * Init product links data (related, upsell, crosssel)
         */
        $links = $this->getRequest()->getPost('links');
        if (isset($links['related']) && !$product->getRelatedReadonly()) {
            $product->setRelatedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['related']));
        }
        if (isset($links['upsell']) && !$product->getUpsellReadonly()) {
            $product->setUpSellLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['upsell']));
        }
        if (isset($links['crosssell']) && !$product->getCrosssellReadonly()) {
            $product->setCrossSellLinkData(Mage::helper('adminhtml/js')
                ->decodeGridSerializedInput($links['crosssell']));
        }
        if (isset($links['grouped']) && !$product->getGroupedReadonly()) {
            $product->setGroupedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['grouped']));
        }

        /**
         * Initialize product categories
         */
        $categoryIds = $this->getRequest()->getPost('category_ids');
        if ($categoryIds !== null) {
            if (empty($categoryIds)) {
                $categoryIds = [];
            }
            $product->setCategoryIds($categoryIds);
        }

        /**
         * Initialize data for configurable product
         */
        if (($data = $this->getRequest()->getPost('configurable_products_data'))
            && !$product->getConfigurableReadonly()
        ) {
            $product->setConfigurableProductsData(Mage::helper('core')->jsonDecode($data));
        }
        if (($data = $this->getRequest()->getPost('configurable_attributes_data'))
            && !$product->getConfigurableReadonly()
        ) {
            $product->setConfigurableAttributesData(Mage::helper('core')->jsonDecode($data));
        }

        $product->setCanSaveConfigurableAttributes(
            (bool) $this->getRequest()->getPost('affect_configurable_product_attributes')
                && !$product->getConfigurableReadonly()
        );

        /**
         * Initialize product options
         */
        if (isset($productData['options']) && !$product->getOptionsReadonly()) {
            $product->setProductOptions($productData['options']);
        }

        $product->setCanSaveCustomOptions(
            (bool)$this->getRequest()->getPost('affect_product_custom_options')
            && !$product->getOptionsReadonly()
        );

        Mage::dispatchEvent(
            'catalog_product_prepare_save',
            ['product' => $product, 'request' => $this->getRequest()]
        );

        return $product;
    }

    /**
     * Filter product stock data
     *
     * @param array $stockData
     */
    protected function _filterStockData(&$stockData)
    {
        if (is_null($stockData)) {
            return;
        }
        if (!isset($stockData['use_config_manage_stock'])) {
            $stockData['use_config_manage_stock'] = 0;
        }
        if (isset($stockData['qty']) && (float)$stockData['qty'] > self::MAX_QTY_VALUE) {
            $stockData['qty'] = self::MAX_QTY_VALUE;
        }
        if (isset($stockData['min_qty']) && (int)$stockData['min_qty'] < 0) {
            $stockData['min_qty'] = 0;
        }
        if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
            $stockData['is_decimal_divided'] = 0;
        }
    }

    public function categoriesJsonAction()
    {
        $product = $this->_initProduct();

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * Save product action
     */
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $productId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);

        $data = $this->getRequest()->getPost();
        if ($data) {
            $this->_filterStockData($data['product']['stock_data']);

            $product = $this->_initProductSave();
            // check sku attribute
            $productSku = $product->getSku();
            if ($productSku && $productSku != Mage::helper('core')->stripTags($productSku)) {
                $this->_getSession()->addError($this->__('HTML tags are not allowed in SKU attribute.'));
                $this->_redirect('*/*/edit', [
                    'id' => $productId,
                    '_current' => true
                ]);
                return;
            }

            try {
                $product->validate();
                $product->save();
                $productId = $product->getId();

                if (isset($data['copy_to_stores'])) {
                   $this->_copyAttributesBetweenStores($data['copy_to_stores'], $product);
                }

                $this->_getSession()->addSuccess($this->__('The product has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setProductData($data);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', [
                'id'    => $productId,
                '_current'=>true
            ]);
        } elseif($this->getRequest()->getParam('popup')) {
            $this->_redirect('*/*/created', [
                '_current'   => true,
                'id'         => $productId,
                'edit'       => $isEdit
            ]);
        } else {
            $this->_redirect('*/*/', ['store'=>$storeId]);
        }
    }

    /**
     * Duplicates product attributes between stores.
     * @param array $stores list of store pairs: array(fromStore => toStore, fromStore => toStore,..)
     * @param Mage_Catalog_Model_Product $product whose attributes should be copied
     * @return $this
     * @throws Throwable
     */
    protected function _copyAttributesBetweenStores(array $stores, Mage_Catalog_Model_Product $product)
    {
        foreach ($stores as $storeTo => $storeFrom) {
            $productInStore = Mage::getModel('catalog/product')
                ->setStoreId($storeFrom)
                ->load($product->getId());
            Mage::dispatchEvent('product_duplicate_attributes', [
                'product' => $productInStore,
                'storeTo' => $storeTo,
                'storeFrom' => $storeFrom,
            ]);
            $productInStore->setStoreId($storeTo)->save();
        }
        return $this;
    }

    /**
     * Create product duplicate
     * @throws Mage_Core_Exception
     */
    public function duplicateAction()
    {
        $product = $this->_initProduct();
        try {
            $newProduct = $product->duplicate();
            $this->_getSession()->addSuccess($this->__('The product has been duplicated.'));
            $this->_redirect('*/*/edit', ['_current'=>true, 'id'=>$newProduct->getId()]);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/edit', ['_current'=>true]);
        }
    }

    /**
     * @deprecated since 1.4.0.0-alpha2
     */
    protected function _decodeInput($encoded)
    {
        parse_str($encoded, $data);
        foreach($data as $key=>$value) {
            parse_str(base64_decode($value), $data[$key]);
        }
        return $data;
    }

    /**
     * Delete product action
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $product = Mage::getModel('catalog/product')
                ->load($id);
            try {
                $product->delete();
                $this->_getSession()->addSuccess($this->__('The product has been deleted.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()
            ->setRedirect($this->getUrl('*/*/', ['store'=>$this->getRequest()->getParam('store')]));
    }

    /**
     * Get tag grid
     */
    public function tagGridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('admin.product.tags')
            ->setProductId($this->getRequest()->getParam('id'));
        $this->renderLayout();
    }

    /**
     * Get alerts price grid
     */
    public function alertsPriceGridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Get alerts stock grid
     */
    public function alertsStockGridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * @deprecated since 1.5.0.0
     * @return $this
     */
    public function addCustomersToAlertQueueAction()
    {
        return $this;
    }

    public function addAttributeAction()
    {
        $this->_getSession()->addNotice(
            Mage::helper('catalog')->__('Please click on the Close Window button if it is not closed automatically.')
        );
        $this->loadLayout('popup');
        $this->_initProduct();
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_new_product_created')
        );
        $this->renderLayout();
    }

    public function createdAction()
    {
        $this->_getSession()->addNotice(
            Mage::helper('catalog')->__('Please click on the Close Window button if it is not closed automatically.')
        );
        $this->loadLayout('popup');
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/catalog_product_created')
        );
        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        } else {
            if (!empty($productIds)) {
                try {
                    foreach ($productIds as $productId) {
                        $product = Mage::getSingleton('catalog/product')->load($productId);
                        Mage::dispatchEvent('catalog_controller_product_delete', ['product' => $product]);
                        $product->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($productIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Update product(s) status action
     *
     */
    public function massStatusAction()
    {
        $productIds = (array)$this->getRequest()->getParam('product');
        $storeId    = (int)$this->getRequest()->getParam('store', 0);
        $status     = (int)$this->getRequest()->getParam('status');

        try {
            $this->_validateMassStatus($productIds, $status);
            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($productIds, ['status' => $status], $storeId);
            Mage::dispatchEvent('catalog_controller_product_mass_status', ['product_ids' => $productIds]);

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($productIds))
            );
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
                ->addException($e, $this->__('An error occurred while updating the product(s) status.'));
        }

        $this->_redirect('*/*/', ['store'=> $storeId]);
    }

    /**
     * Validate batch of products before theirs status will be set
     *
     * @throws Mage_Core_Exception
     * @param  array $productIds
     * @param  int $status
     */
    public function _validateMassStatus(array $productIds, $status)
    {
        if ($status == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            if (!Mage::getModel('catalog/product')->isProductsHasSku($productIds)) {
                throw new Mage_Core_Exception(
                    $this->__('Some of the processed products have no SKU value defined. Please fill it prior to performing operations on these products.')
                );
            }
        }
    }

    /**
     * Get tag customer grid
     *
     */
    public function tagCustomerGridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('admin.product.tags.customers')
                ->setProductId($this->getRequest()->getParam('id'));
        $this->renderLayout();
    }

    public function quickCreateAction()
    {
        $result = [];

        /** @var Mage_Catalog_Model_Product $configurableProduct */
        $configurableProduct = Mage::getModel('catalog/product')
            ->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)
            ->load($this->getRequest()->getParam('product'));

        if (!$configurableProduct->isConfigurable()) {
            // If invalid parent product
            $this->_redirect('*/*/');
            return;
        }

        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')
            ->setStoreId(0)
            ->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->setAttributeSetId($configurableProduct->getAttributeSetId());

        foreach ($product->getTypeInstance()->getEditableAttributes() as $attribute) {
            if ($attribute->getIsUnique()
                || $attribute->getAttributeCode() == 'url_key'
                || $attribute->getFrontend()->getInputType() == 'gallery'
                || $attribute->getFrontend()->getInputType() == 'media_image'
                || !$attribute->getIsVisible()) {
                continue;
            }

            $product->setData(
                $attribute->getAttributeCode(),
                $configurableProduct->getData($attribute->getAttributeCode())
            );
        }

        $product->addData($this->getRequest()->getParam('simple_product', []));

        $productSku = $product->getSku();
        if ($productSku && $productSku != Mage::helper('core')->stripTags($productSku)) {
            $result['error'] = [
                'message' => $this->__('HTML tags are not allowed in SKU attribute.')
            ];
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            return;
        }

        $product->setWebsiteIds($configurableProduct->getWebsiteIds());

        $autogenerateOptions = [];
        $result['attributes'] = [];

        foreach ($configurableProduct->getTypeInstance()->getConfigurableAttributes() as $attribute) {
            $value = $product->getAttributeText($attribute->getProductAttribute()->getAttributeCode());
            $autogenerateOptions[] = $value;
            $result['attributes'][] = [
                'label'         => $value,
                'value_index'   => $product->getData($attribute->getProductAttribute()->getAttributeCode()),
                'attribute_id'  => $attribute->getProductAttribute()->getId()
            ];
        }

        if ($product->getNameAutogenerate()) {
            $product->setName($configurableProduct->getName() . '-' . implode('-', $autogenerateOptions));
        }

        if ($product->getSkuAutogenerate()) {
            $product->setSku($configurableProduct->getSku() . '-' . implode('-', $autogenerateOptions));
        }

        if (is_array($product->getPricing())) {
           $result['pricing'] = $product->getPricing();
           $additionalPrice = 0;
           foreach ($product->getPricing() as $pricing) {
               if (empty($pricing['value'])) {
                   continue;
               }

               if (!empty($pricing['is_percent'])) {
                   $pricing['value'] = ($pricing['value']/100)*$product->getPrice();
               }

               $additionalPrice += $pricing['value'];
           }

           $product->setPrice($product->getPrice() + $additionalPrice);
           $product->unsPricing();
        }

        try {
            $product->validate();
            $product->save();
            $result['product_id'] = $product->getId();
            $this->_getSession()->addSuccess(Mage::helper('catalog')->__('The product has been created.'));
            $this->_initLayoutMessages('adminhtml/session');
            $result['messages']  = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = [
                'message' =>  $e->getMessage(),
                'fields'  => [
                    'sku'  =>  $product->getSku()
                ]
            ];

        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = [
                'message'   =>  $this->__('An error occurred while saving the product. ') . $e->getMessage()
            ];
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * Show item update result from updateAction
     * in Wishlist and Cart controllers.
     *
     */
    public function showUpdateResultAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        if ($session->hasCompositeProductResult() && $session->getCompositeProductResult() instanceof Varien_Object){
            /** @var Mage_Adminhtml_Helper_Catalog_Product_Composite $helper */
            $helper = Mage::helper('adminhtml/catalog_product_composite');
            $helper->renderUpdateResult($this, $session->getCompositeProductResult());
            $session->unsCompositeProductResult();
        } else {
            $session->unsCompositeProductResult();
            return false;
        }
    }
}
