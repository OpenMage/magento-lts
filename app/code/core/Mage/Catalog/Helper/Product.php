<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog category helper
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Helper_Product extends Mage_Core_Helper_Url
{
    public const XML_PATH_PRODUCT_URL_SUFFIX           = 'catalog/seo/product_url_suffix';

    public const XML_PATH_PRODUCT_URL_USE_CATEGORY     = 'catalog/seo/product_use_categories';

    public const XML_PATH_USE_PRODUCT_CANONICAL_TAG    = 'catalog/seo/product_canonical_tag';

    public const DEFAULT_QTY                           = 1;

    protected $_moduleName = 'Mage_Catalog';

    /**
     * Flag that shows if Magento has to check product to be saleable (enabled and/or inStock)
     *
     * @var bool
     */
    protected $_skipSaleableCheck = false;

    /**
     * Cache for product rewrite suffix
     *
     * @var array
     */
    protected $_productUrlSuffix = [];

    protected $_statuses;

    protected $_priceBlock;

    /**
     * Retrieve product view page url
     *
     * @param   int|Mage_Catalog_Model_Product|string $product
     * @return  false|string
     * @throws  Mage_Core_Exception
     */
    public function getProductUrl($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            return $product->getProductUrl();
        } elseif (is_numeric($product)) {
            return Mage::getModel('catalog/product')->load($product)->getProductUrl();
        }

        return false;
    }

    /**
     * Retrieve product view page url including provided category Id
     *
     * @param   int|string $productId
     * @param   int|string $categoryId
     * @return  string
     * @throws  Mage_Core_Exception
     */
    public function getFullProductUrl($productId, $categoryId = null)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        if ($categoryId && $product->canBeShowInCategory($categoryId)) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $product->setCategory($category);
        }

        return $product->getProductUrl();
    }

    /**
     * Retrieve product price
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  float
     */
    public function getPrice($product)
    {
        return $product->getPrice();
    }

    /**
     * Retrieve product final price
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  float
     */
    public function getFinalPrice($product)
    {
        return $product->getFinalPrice();
    }

    /**
     * Retrieve base image url
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return string
     * @throws Exception
     */
    public function getImageUrl($product)
    {
        $url = false;
        if (!$product->getImage()) {
            $url = Mage::getDesign()->getSkinUrl('images/no_image.jpg');
        } elseif ($attribute = $product->getResource()->getAttribute('image')) {
            $url = $attribute->getFrontend()->getUrl($product);
        }

        return $url;
    }

    /**
     * Retrieve small image url
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return string
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function getSmallImageUrl($product)
    {
        $url = false;
        if (!$product->getSmallImage()) {
            $url = Mage::getDesign()->getSkinUrl('images/no_image.jpg');
        } elseif ($attribute = $product->getResource()->getAttribute('small_image')) {
            $url = $attribute->getFrontend()->getUrl($product);
        }

        return $url;
    }

    /**
     * Retrieve thumbnail image url
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return string
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function getThumbnailUrl($product)
    {
        $url = false;
        if (!$product->getThumbnail()) {
            $url = Mage::getDesign()->getSkinUrl('images/no_image.jpg');
        } elseif ($attribute = $product->getResource()->getAttribute('thumbnail')) {
            $url = $attribute->getFrontend()->getUrl($product);
        }

        return $url;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getEmailToFriendUrl($product)
    {
        $categoryId = null;
        if ($category = Mage::registry('current_category')) {
            $categoryId = $category->getId();
        }

        return $this->_getUrl('sendfriend/product/send', [
            'id' => $product->getId(),
            'cat_id' => $categoryId,
        ]);
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        if (is_null($this->_statuses)) {
            $this->_statuses = [];//Mage::getModel('catalog/product_status')->getResourceCollection()->load();
        }

        return $this->_statuses;
    }

    /**
     * Check if a product can be shown
     *
     * @param int|Mage_Catalog_Model_Product $product
     * @param string $where
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function canShow($product, $where = 'catalog')
    {
        if (is_int($product)) {
            $product = Mage::getModel('catalog/product')->load($product);
        }

        /** @var Mage_Catalog_Model_Product $product */
        if (!$product->getId()) {
            return false;
        }

        return $product->isVisibleInCatalog() && $product->isVisibleInSiteVisibility();
    }

    /**
     * Retrieve product rewrite sufix for store
     *
     * @param int $storeId
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getProductUrlSuffix($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        if (!isset($this->_productUrlSuffix[$storeId])) {
            $this->_productUrlSuffix[$storeId] = Mage::getStoreConfig(self::XML_PATH_PRODUCT_URL_SUFFIX, $storeId);
        }

        return $this->_productUrlSuffix[$storeId];
    }

    /**
     * Check if <link rel="canonical"> can be used for product
     *
     * @param null|bool|int|Mage_Core_Model_Store|string $store
     * @return bool
     */
    public function canUseCanonicalTag($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_PRODUCT_CANONICAL_TAG, $store);
    }

    /**
     * Return information array of product attribute input types
     * Only a small number of settings returned, so we won't break anything in current dataflow
     * As soon as development process goes on we need to add there all possible settings
     *
     * @param string $inputType
     * @return array
     */
    public function getAttributeInputTypes($inputType = null)
    {
        /**
         * @todo specify there all relations for properties depending on input type
         */
        $inputTypes = [
            'multiselect'   => [
                'backend_model'     => 'eav/entity_attribute_backend_array',
            ],
            'boolean'       => [
                'source_model'      => 'eav/entity_attribute_source_boolean',
            ],
        ];

        if (is_null($inputType)) {
            return $inputTypes;
        } elseif (isset($inputTypes[$inputType])) {
            return $inputTypes[$inputType];
        }

        return [];
    }

    /**
     * Return default attribute backend model by input type
     *
     * @param string $inputType
     * @return null|string
     */
    public function getAttributeBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }

        return null;
    }

    /**
     * Return default attribute source model by input type
     *
     * @param string $inputType
     * @return null|string
     */
    public function getAttributeSourceModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['source_model'])) {
            return $inputTypes[$inputType]['source_model'];
        }

        return null;
    }

    /**
     * Inits product to be used for product controller actions and layouts
     * $params can have following data:
     *   'category_id' - id of category to check and append to product as current.
     *     If empty (except FALSE) - will be guessed (e.g. from last visited) to load as current.
     *
     * @param int $productId
     * @param Mage_Core_Controller_Front_Action $controller
     * @param Varien_Object $params
     *
     * @return false|Mage_Catalog_Model_Product
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function initProduct($productId, $controller, $params = null)
    {
        // Prepare data for routine
        if (!$params) {
            $params = new Varien_Object();
        }

        if (!$productId) {
            return false;
        }

        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);

        // Init and load product
        Mage::dispatchEvent('catalog_controller_product_init_before', [
            'controller_action' => $controller,
            'params' => $params,
            'product' => $product,
        ]);

        if (!$this->canShow($product)) {
            return false;
        }

        if (!in_array(Mage::app()->getStore()->getWebsiteId(), $product->getWebsiteIds())) {
            return false;
        }

        // Load product current category
        $categoryId = $params->getCategoryId();
        if (!$categoryId && ($categoryId !== false)) {
            $lastId = Mage::getSingleton('catalog/session')->getLastVisitedCategoryId();
            if ($product->canBeShowInCategory($lastId)) {
                $categoryId = $lastId;
            }
        } elseif (!$product->canBeShowInCategory($categoryId)) {
            $categoryId = null;
        }

        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $product->setCategory($category);
            Mage::register('current_category', $category);
            Mage::register('current_entity_key', $category->getPath());
        }

        // Register current data and dispatch final events
        Mage::register('current_product', $product);
        Mage::register('product', $product);

        try {
            Mage::dispatchEvent('catalog_controller_product_init', ['product' => $product]);
            Mage::dispatchEvent(
                'catalog_controller_product_init_after',
                ['product' => $product,
                    'controller_action' => $controller,
                ],
            );
        } catch (Mage_Core_Exception $mageCoreException) {
            Mage::logException($mageCoreException);
            return false;
        }

        return $product;
    }

    /**
     * Prepares product options by buyRequest: retrieves values and assigns them as default.
     * Also parses and adds product management related values - e.g. qty
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  Varien_Object $buyRequest
     * @return $this
     */
    public function prepareProductOptions($product, $buyRequest)
    {
        $optionValues = $product->processBuyRequest($buyRequest);
        $optionValues->setQty($buyRequest->getQty());

        $product->setPreconfiguredValues($optionValues);

        return $this;
    }

    /**
     * Process $buyRequest and sets its options before saving configuration to some product item.
     * This method is used to attach additional parameters to processed buyRequest.
     *
     * $params holds parameters of what operation must be performed:
     * - 'current_config', Varien_Object or array - current buyRequest that configures product in this item,
     *   used to restore currently attached files
     * - 'files_prefix': string[a-z0-9_] - prefix that was added at frontend to names of file inputs,
     *   so they won't intersect with other submitted options
     *
     * @param array|Varien_Object $buyRequest
     * @param array|Varien_Object $params
     * @return Varien_Object
     */
    public function addParamsToBuyRequest($buyRequest, $params)
    {
        if (is_array($buyRequest)) {
            $buyRequest = new Varien_Object($buyRequest);
        }

        if (is_array($params)) {
            $params = new Varien_Object($params);
        }

        // Ensure that currentConfig goes as Varien_Object - for easier work with it later
        $currentConfig = $params->getCurrentConfig();
        if ($currentConfig) {
            if (is_array($currentConfig)) {
                $params->setCurrentConfig(new Varien_Object($currentConfig));
            } elseif (!($currentConfig instanceof Varien_Object)) {
                $params->unsCurrentConfig();
            }
        }

        /*
         * Notice that '_processing_params' must always be object to protect processing forged requests
         * where '_processing_params' comes in $buyRequest as array from user input
         */
        $processingParams = $buyRequest->getData('_processing_params');
        if (!$processingParams || !($processingParams instanceof Varien_Object)) {
            $processingParams = new Varien_Object();
            $buyRequest->setData('_processing_params', $processingParams);
        }

        $processingParams->addData($params->getData());

        return $buyRequest;
    }

    /**
     * Return loaded product instance
     *
     * @param  int|string $productId (SKU or ID)
     * @param  null|int $store
     * @param  string $identifierType
     * @return Mage_Catalog_Model_Product
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getProduct($productId, $store, $identifierType = null)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore($store)->getId());

        $expectedIdType = false;
        if ($identifierType === null) {
            if (is_string($productId) && !preg_match('/^[+-]?[1-9]\d*$|^0$/', $productId)) {
                $expectedIdType = 'sku';
            }
        }

        if ($identifierType == 'sku' || $expectedIdType == 'sku') {
            $idBySku = $product->getIdBySku($productId);
            if ($idBySku) {
                $productId = $idBySku;
            } elseif ($identifierType == 'sku') {
                // Return empty product because it was not found by originally specified SKU identifier
                return $product;
            }
        }

        if ($productId && is_numeric($productId)) {
            $product->load((int) $productId);
        }

        return $product;
    }

    /**
     * Set flag that shows if Magento has to check product to be saleable (enabled and/or inStock)
     *
     * For instance, during order creation in the backend admin has ability to add any products to order
     *
     * @param bool $skipSaleableCheck
     * @return $this
     */
    public function setSkipSaleableCheck($skipSaleableCheck = false)
    {
        $this->_skipSaleableCheck = $skipSaleableCheck;
        return $this;
    }

    /**
     * Get flag that shows if Magento has to check product to be saleable (enabled and/or inStock)
     *
     * @return bool
     */
    public function getSkipSaleableCheck()
    {
        return $this->_skipSaleableCheck;
    }

    /**
     * Gets minimal sales quantity
     *
     * @param Mage_Catalog_Model_Product $product
     * @return null|float
     */
    public function getMinimalQty($product)
    {
        $stockItem = $product->getStockItem();
        if ($stockItem && $stockItem->getMinSaleQty()) {
            return $stockItem->getMinSaleQty() * 1;
        }

        return null;
    }

    /**
     * Get default qty - either as preconfigured, or as 1.
     * Also restricts it by minimal qty.
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float|int
     */
    public function getDefaultQty($product)
    {
        $qty = $this->getMinimalQty($product);
        $configQty = $product->getPreconfiguredValues()->getQty();

        if ($product->isConfigurable() || $configQty > $qty) {
            $qty = $configQty;
        }

        if (is_null($qty)) {
            $qty = self::DEFAULT_QTY;
        }

        return $qty;
    }

    /**
     * Get default product value by field name
     *
     * @param string $fieldName
     * @param string $productType
     * @return int
     */
    public function getDefaultProductValue($fieldName, $productType)
    {
        $fieldData = $this->getFieldset($fieldName) ? (array) $this->getFieldset($fieldName) : null;
        if (!empty($fieldData)
            && ((is_array($fieldData['product_type']) && array_key_exists($productType, $fieldData['product_type'])) || (is_object($fieldData['product_type']) && property_exists($fieldData['product_type'], $productType)))
            && (bool) $fieldData['use_config']
        ) {
            return $fieldData['inventory'];
        }

        return self::DEFAULT_QTY;
    }

    /**
     * Return array from config by fieldset name and area
     *
     * @param null|string $field
     * @param string $fieldset
     * @param string $area
     * @return null|array
     */
    public function getFieldset($field = null, $fieldset = 'catalog_product_dataflow', $area = 'admin')
    {
        $fieldsetData = Mage::getConfig()->getFieldset($fieldset, $area);
        if ($fieldsetData) {
            return $fieldsetData ? $fieldsetData->$field : $fieldsetData;
        }

        return $fieldsetData;
    }
}
