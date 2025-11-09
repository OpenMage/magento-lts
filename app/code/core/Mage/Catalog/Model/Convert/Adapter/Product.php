<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Class Mage_Catalog_Model_Convert_Adapter_Product
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Convert_Adapter_Product extends Mage_Eav_Model_Convert_Adapter_Entity
{
    public const MULTI_DELIMITER   = ' , ';

    public const ENTITY            = 'catalog_product_import';

    protected $_eventPrefix = 'catalog_product_import';

    /**
     * Product model
     *
     * @var null|Mage_Catalog_Model_Product|string
     */
    protected $_productModel;

    /**
     * product types collection array
     *
     * @var null|array
     */
    protected $_productTypes;

    /**
     * Product Type Instances singletons
     *
     * @var array
     */
    protected $_productTypeInstances = [];

    /**
     * product attribute set collection array
     *
     * @var null|array
     */
    protected $_productAttributeSets;

    protected $_stores;

    /**
     * @var array
     */
    protected $_storesIdCode = [];

    protected $_attributes = [];

    protected $_configs = [];

    protected $_requiredFields = [];

    protected $_ignoreFields = [];

    /**
     * @deprecated after 1.5.0.0-alpha2
     *
     * @var array
     */
    protected $_imageFields = [];

    /**
     * Inventory Fields array
     *
     * @var array
     */
    protected $_inventoryFields             = [];

    /**
     * Inventory Fields by product Types
     *
     * @var array
     */
    protected $_inventoryFieldsProductTypes = [];

    protected $_toNumber = [];

    /**
     * Gallery backend model
     *
     * @var Mage_Catalog_Model_Product_Attribute_Backend_Media
     */
    protected $_galleryBackendModel;

    /**
     * Retrieve event prefix for adapter
     *
     * @return string
     */
    public function getEventPrefix()
    {
        return $this->_eventPrefix;
    }

    /**
     * Affected entity ids
     *
     * @var array
     */
    protected $_affectedEntityIds = [];

    /**
     * Store affected entity ids
     *
     * @param  array|int $ids
     * @return $this
     */
    protected function _addAffectedEntityIds($ids)
    {
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $this->_addAffectedEntityIds($id);
            }
        } else {
            $this->_affectedEntityIds[] = $ids;
        }

        return $this;
    }

    /**
     * Retrieve affected entity ids
     *
     * @return array
     */
    public function getAffectedEntityIds()
    {
        return $this->_affectedEntityIds;
    }

    /**
     * Clear affected entity ids results
     *
     * @return $this
     */
    public function clearAffectedEntityIds()
    {
        $this->_affectedEntityIds = [];
        return $this;
    }

    /**
     * Load product collection Id(s)
     */
    public function load()
    {
        $attrFilterArray = [];
        $attrFilterArray ['name']           = 'like';
        $attrFilterArray ['sku']            = 'startsWith';
        $attrFilterArray ['type']           = 'eq';
        $attrFilterArray ['attribute_set']  = 'eq';
        $attrFilterArray ['visibility']     = 'eq';
        $attrFilterArray ['status']         = 'eq';
        $attrFilterArray ['price']          = 'fromTo';
        $attrFilterArray ['qty']            = 'fromTo';
        $attrFilterArray ['store_id']       = 'eq';

        $attrToDb = [
            'type'          => 'type_id',
            'attribute_set' => 'attribute_set_id',
        ];

        $filters = $this->_parseVars();

        if ($qty = $this->getFieldValue($filters, 'qty')) {
            $qtyFrom = isset($qty['from']) ? (float) $qty['from'] : 0;
            $qtyTo   = isset($qty['to']) ? (float) $qty['to'] : 0;

            $qtyAttr = [];
            $qtyAttr['alias']       = 'qty';
            $qtyAttr['attribute']   = 'cataloginventory/stock_item';
            $qtyAttr['field']       = 'qty';
            $qtyAttr['bind']        = 'product_id=entity_id';
            $qtyAttr['cond']        = "{{table}}.qty between '{$qtyFrom}' AND '{$qtyTo}'";
            $qtyAttr['joinType']    = 'inner';

            $this->setJoinField($qtyAttr);
        }

        parent::setFilter($attrFilterArray, $attrToDb);

        if ($price = $this->getFieldValue($filters, 'price')) {
            $this->_filter[] = [
                'attribute' => 'price',
                'from'      => $price['from'],
                'to'        => $price['to'],
            ];
            $this->setJoinAttr([
                'alias'     => 'price',
                'attribute' => 'catalog_product/price',
                'bind'      => 'entity_id',
                'joinType'  => 'LEFT',
            ]);
        }

        return parent::load();
    }

    /**
     * Retrieve product model cache
     *
     * @return Mage_Catalog_Model_Product|object
     */
    public function getProductModel()
    {
        if (is_null($this->_productModel)) {
            $productModel = Mage::getModel('catalog/product');
            $this->_productModel = Mage::objects()->save($productModel);
        }

        return Mage::objects()->load($this->_productModel);
    }

    /**
     * Retrieve eav entity attribute model
     *
     * @param string $code
     * @return false|Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute($code)
    {
        if (!isset($this->_attributes[$code])) {
            $this->_attributes[$code] = $this->getProductModel()->getResource()->getAttribute($code);
        }

        if ($this->_attributes[$code] instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
            $applyTo = $this->_attributes[$code]->getApplyTo();
            if ($applyTo && !in_array($this->getProductModel()->getTypeId(), $applyTo)) {
                return false;
            }
        }

        return $this->_attributes[$code];
    }

    /**
     * Retrieve product type collection array
     *
     * @return array
     */
    public function getProductTypes()
    {
        if (is_null($this->_productTypes)) {
            $this->_productTypes = [];
            $options = Mage::getModel('catalog/product_type')
                ->getOptionArray();
            foreach ($options as $k => $v) {
                $this->_productTypes[$k] = $k;
            }
        }

        return $this->_productTypes;
    }

    /**
     * ReDefine Product Type Instance to Product
     *
     * @return $this
     */
    public function setProductTypeInstance(Mage_Catalog_Model_Product $product)
    {
        $type = $product->getTypeId();
        if (!isset($this->_productTypeInstances[$type])) {
            $this->_productTypeInstances[$type] = Mage::getSingleton('catalog/product_type')
                ->factory($product, true);
        }

        $product->setTypeInstance($this->_productTypeInstances[$type], true);
        return $this;
    }

    /**
     * Retrieve product attribute set collection array
     *
     * @return array
     */
    public function getProductAttributeSets()
    {
        if (is_null($this->_productAttributeSets)) {
            $this->_productAttributeSets = [];

            $entityTypeId = Mage::getModel('eav/entity')
                ->setType('catalog_product')
                ->getTypeId();
            $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter($entityTypeId);
            /** @var Mage_Eav_Model_Entity_Attribute_Set $set */
            foreach ($collection as $set) {
                $this->_productAttributeSets[$set->getAttributeSetName()] = $set->getId();
            }
        }

        return $this->_productAttributeSets;
    }

    /**
     *  Init stores
     */
    protected function _initStores()
    {
        if (is_null($this->_stores)) {
            $this->_stores = Mage::app()->getStores(true, true);
            foreach ($this->_stores as $code => $store) {
                $this->_storesIdCode[$store->getId()] = $code;
            }
        }
    }

    /**
     * Retrieve store object by code
     *
     * @param string $store
     * @return false|Mage_Core_Model_Store
     */
    public function getStoreByCode($store)
    {
        $this->_initStores();
        /**
         * In single store mode all data should be saved as default
         */
        if (Mage::app()->isSingleStoreMode()) {
            return Mage::app()->getStore(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);
        }

        return $this->_stores[$store] ?? false;
    }

    /**
     * Retrieve store object by code
     *
     * @param string $id
     * @return false|Mage_Core_Model_Store
     */
    public function getStoreById($id)
    {
        $this->_initStores();
        /**
         * In single store mode all data should be saved as default
         */
        if (Mage::app()->isSingleStoreMode()) {
            return Mage::app()->getStore(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID);
        }

        if (isset($this->_storesIdCode[$id])) {
            return $this->getStoreByCode($this->_storesIdCode[$id]);
        }

        return false;
    }

    public function parse()
    {
        $batchModel = Mage::getSingleton('dataflow/batch');
        /** @var Mage_Dataflow_Model_Batch $batchModel */

        $batchImportModel = $batchModel->getBatchImportModel();
        $importIds = $batchImportModel->getIdCollection();

        foreach ($importIds as $importId) {
            // phpcs:ignore Ecg.Performance.Loop.ModelLSD
            $batchImportModel->load($importId);
            $importData = $batchImportModel->getBatchData();

            $this->saveRow($importData);
        }
    }

    protected $_productId = '';

    /**
     * Initialize convert adapter model for products collection
     */
    public function __construct()
    {
        $fieldset = Mage::getConfig()->getFieldset('catalog_product_dataflow', 'admin');
        foreach ($fieldset as $code => $node) {
            /** @var Mage_Core_Model_Config_Element $node */
            if ($node->is('inventory')) {
                foreach ($node->product_type->children() as $productType) {
                    $productType = $productType->getName();
                    $this->_inventoryFieldsProductTypes[$productType][] = $code;
                    if ($node->is('use_config')) {
                        $this->_inventoryFieldsProductTypes[$productType][] = 'use_config_' . $code;
                    }
                }

                $this->_inventoryFields[] = $code;
                if ($node->is('use_config')) {
                    $this->_inventoryFields[] = 'use_config_' . $code;
                }
            }

            if ($node->is('required')) {
                $this->_requiredFields[] = $code;
            }

            if ($node->is('ignore')) {
                $this->_ignoreFields[] = $code;
            }

            if ($node->is('to_number')) {
                $this->_toNumber[] = $code;
            }
        }

        $this->setVar('entity_type', 'catalog/product');
        if (!Mage::registry('Object_Cache_Product')) {
            $this->setProduct(Mage::getModel('catalog/product'));
        }

        if (!Mage::registry('Object_Cache_StockItem')) {
            $this->setStockItem(Mage::getModel('cataloginventory/stock_item'));
        }

        $this->_galleryBackendModel = $this->getAttribute('media_gallery')->getBackend();
    }

    /**
     * Retrieve not loaded collection
     *
     * @param string $entityType
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getCollectionForLoad($entityType)
    {
        return parent::_getCollectionForLoad($entityType)
            ->setStoreId($this->getStoreId())
            ->addStoreFilter($this->getStoreId());
    }

    /**
     * @throws Mage_Core_Exception
     * @throws Varien_Exception
     */
    public function setProduct(Mage_Catalog_Model_Product $object)
    {
        $id = Mage::objects()->save($object);
        //$this->_product = $object;
        Mage::register('Object_Cache_Product', $id);
    }

    /**
     * @return object
     */
    public function getProduct()
    {
        return Mage::objects()->load(Mage::registry('Object_Cache_Product'));
    }

    /**
     * @throws Mage_Core_Exception
     * @throws Varien_Exception
     */
    public function setStockItem(Mage_CatalogInventory_Model_Stock_Item $object)
    {
        $id = Mage::objects()->save($object);
        Mage::register('Object_Cache_StockItem', $id);
    }

    /**
     * @return object
     */
    public function getStockItem()
    {
        return Mage::objects()->load(Mage::registry('Object_Cache_StockItem'));
    }

    /**
     * @return $this|Mage_Eav_Model_Convert_Adapter_Entity
     */
    public function save()
    {
        $stores = [];
        foreach (Mage::getConfig()->getNode('stores')->children() as $storeNode) {
            $stores[(int) $storeNode->system->store->id] = $storeNode->getName();
        }

        $collections = $this->getData();
        if ($collections instanceof Mage_Catalog_Model_Resource_Product_Collection) {
            $collections = [$collections->getEntity()->getStoreId() => $collections];
        } elseif (!is_array($collections)) {
            $this->addException(
                Mage::helper('catalog')->__('No product collections found.'),
                Mage_Dataflow_Model_Convert_Exception::FATAL,
            );
        }

        $stockItems = Mage::registry('current_imported_inventory');
        if ($collections) {
            foreach ($collections as $storeId => $collection) {
                $this->addException(Mage::helper('catalog')->__('Records for "%s" store found.', $stores[$storeId]));

                if (!$collection instanceof Mage_Catalog_Model_Resource_Product_Collection) {
                    $this->addException(
                        Mage::helper('catalog')->__('Product collection expected.'),
                        Mage_Dataflow_Model_Convert_Exception::FATAL,
                    );
                }

                try {
                    $i = 0;
                    foreach ($collection->getIterator() as $model) {
                        $new = false;
                        // if product is new, create default values first
                        if (!$model->getId()) {
                            $new = true;
                            // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                            $model->save();

                            // if new product and then store is not default
                            // we duplicate product as default product with store_id -
                            if ($storeId !== 0) {
                                $data = $model->getData();
                                $default = Mage::getModel('catalog/product');
                                $default->setData($data);
                                $default->setStoreId(0);
                                // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                                $default->save();
                                unset($default);
                            } // end

                            #Mage::getResourceSingleton('catalog_entity/convert')->addProductToStore($model->getId(), 0);
                        }

                        if (!$new || $storeId !== 0) {
                            if ($storeId !== 0) {
                                Mage::getResourceSingleton('catalog_entity/convert')->addProductToStore(
                                    $model->getId(),
                                    $storeId,
                                );
                            }

                            // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                            $model->save();
                        }

                        if (isset($stockItems[$model->getSku()]) && $stock = $stockItems[$model->getSku()]) {
                            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($model->getId());
                            $stockItemId = $stockItem->getId();

                            if (!$stockItemId) {
                                $stockItem->setData('product_id', $model->getId());
                                $stockItem->setData('stock_id', 1);
                                $data = [];
                            } else {
                                $data = $stockItem->getData();
                            }

                            foreach ($stock as $field => $value) {
                                if (!$stockItemId) {
                                    if (in_array($field, $this->_configs)) {
                                        $stockItem->setData('use_config_' . $field, 0);
                                    }

                                    $stockItem->setData($field, $value ? $value : 0);
                                } elseif (in_array($field, $this->_configs)) {
                                    if ($data['use_config_' . $field] == 0) {
                                        $stockItem->setData($field, $value ? $value : 0);
                                    }
                                } else {
                                    $stockItem->setData($field, $value ? $value : 0);
                                }
                            }

                            // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                            $stockItem->save();
                            unset($data);
                            unset($stockItem);
                            unset($stockItemId);
                        }

                        unset($model);
                        $i++;
                    }

                    $this->addException(Mage::helper('catalog')->__('Saved %d record(s)', $i));
                } catch (Exception $e) {
                    if (!$e instanceof Mage_Dataflow_Model_Convert_Exception) {
                        $this->addException(
                            Mage::helper('catalog')->__('An error occurred while saving the collection, aborting. Error message: %s', $e->getMessage()),
                            Mage_Dataflow_Model_Convert_Exception::FATAL,
                        );
                    }
                }
            }
        }

        unset($collections);

        return $this;
    }

    /**
     * Save data row with gallery image info only
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $importData
     *
     * @return $this
     */
    public function saveImageDataRow($product, $importData)
    {
        $imageData = [
            'label'         => $importData['_media_lable'],
            'position'      => $importData['_media_position'],
            'disabled'      => $importData['_media_is_disabled'],
        ];

        $imageFile = trim($importData['_media_image']);
        $imageFile = ltrim($imageFile, DS);

        $imageFilePath = Mage::getBaseDir('media') . DS . 'import' . DS . $imageFile;

        $updatedFileName = $this->_galleryBackendModel->addImage(
            $product,
            $imageFilePath,
            null,
            false,
            (bool) $importData['_media_is_disabled'],
        );
        $this->_galleryBackendModel->updateImage($product, $updatedFileName, $imageData);

        $this->_addAffectedEntityIds($product->getId());
        $product->setIsMassupdate(true)
            ->setExcludeUrlRewrite(true)
            ->save();

        return $this;
    }

    /**
     * Save product (import)
     *
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function saveRow(array $importData)
    {
        $product = $this->getProductModel()
            ->reset();

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'store');
                Mage::throwException($message);
            }
        } else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__('Skipping import row, store "%s" field does not exist.', $importData['store']);
            Mage::throwException($message);
        }

        if (empty($importData['sku'])) {
            $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'sku');
            Mage::throwException($message);
        }

        $product->setStoreId($store->getId());
        $productId = $product->getIdBySku($importData['sku']);

        if ($productId) {
            $product->load($productId);
        } else {
            $productTypes = $this->getProductTypes();
            $productAttributeSets = $this->getProductAttributeSets();

            /**
             * Check product define type
             */
            if (empty($importData['type']) || !isset($productTypes[strtolower($importData['type'])])) {
                $value = $importData['type'] ?? '';
                $message = Mage::helper('catalog')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'type');
                Mage::throwException($message);
            }

            $product->setTypeId($productTypes[strtolower($importData['type'])]);
            /**
             * Check product define attribute set
             */
            if (empty($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
                $value = $importData['attribute_set'] ?? '';
                $message = Mage::helper('catalog')->__('Skip import row, the value "%s" is invalid for field "%s"', $value, 'attribute_set');
                Mage::throwException($message);
            }

            $product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);

            foreach ($this->_requiredFields as $field) {
                $attribute = $this->getAttribute($field);
                if (!isset($importData[$field]) && $attribute && $attribute->getIsRequired()) {
                    $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" for new products is not defined.', $field);
                    Mage::throwException($message);
                }
            }
        }

        // process row with media data only
        if (isset($importData['_media_image']) && strlen($importData['_media_image'])) {
            $this->saveImageDataRow($product, $importData);
            return true;
        }

        $this->setProductTypeInstance($product);

        if (isset($importData['category_ids'])) {
            $product->setCategoryIds($importData['category_ids']);
        }

        foreach ($this->_ignoreFields as $field) {
            if (isset($importData[$field])) {
                unset($importData[$field]);
            }
        }

        if ($store->getId() != 0) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = [];
            }

            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }

            $product->setWebsiteIds($websiteIds);
        }

        if (isset($importData['websites'])) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds) || !$store->getId()) {
                $websiteIds = [];
            }

            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) {
                        $websiteIds[] = $website->getId();
                    }
                } catch (Exception) {
                }
            }

            $product->setWebsiteIds($websiteIds);
            unset($websiteIds);
        }

        foreach ($importData as $field => $value) {
            if (in_array($field, $this->_inventoryFields)) {
                continue;
            }

            if (is_null($value)) {
                continue;
            }

            $attribute = $this->getAttribute($field);
            if (!$attribute) {
                continue;
            }

            $isArray = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value);
                $isArray = true;
                $setValue = [];
            }

            if ($value && $attribute->getBackendType() == 'decimal') {
                $setValue = $this->getNumber($value);
            }

            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);

                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if (is_array($item['value'])) {
                            foreach ($item['value'] as $subValue) {
                                if (isset($subValue['value']) && $subValue['value'] == $value) {
                                    $setValue = $value;
                                }
                            }
                        } elseif ($item['label'] == $value) {
                            $setValue = $item['value'];
                        }
                    }
                }
            }

            $product->setData($field, $setValue);
        }

        if (!$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        }

        $stockData = [];
        $inventoryFields = $this->_inventoryFieldsProductTypes[$product->getTypeId()] ?? [];
        foreach ($inventoryFields as $field) {
            if (isset($importData[$field])) {
                if (in_array($field, $this->_toNumber)) {
                    $stockData[$field] = $this->getNumber($importData[$field]);
                } else {
                    $stockData[$field] = $importData[$field];
                }
            }
        }

        $product->setStockData($stockData);

        $arrayToMassAdd = [];

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            if (isset($importData[$mediaAttributeCode])) {
                $file = trim($importData[$mediaAttributeCode]);
                if (!empty($file) && !$this->_galleryBackendModel->getImage($product, $file)) {
                    $arrayToMassAdd[] = ['file' => trim($file), 'mediaAttribute' => $mediaAttributeCode];
                }
            }
        }

        $addedFilesCorrespondence = $this->_galleryBackendModel->addImagesWithDifferentMediaAttributes(
            $product,
            $arrayToMassAdd,
            Mage::getBaseDir('media') . DS . 'import',
            false,
            false,
        );

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            $addedFile = '';
            if (isset($importData[$mediaAttributeCode . '_label'])) {
                $fileLabel = trim($importData[$mediaAttributeCode . '_label']);
                if (isset($importData[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search(
                        $importData[$mediaAttributeCode],
                        $addedFilesCorrespondence['alreadyAddedFiles'],
                    );
                    if ($keyInAddedFile !== false) {
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                    }
                }

                if (!$addedFile) {
                    $addedFile = $product->getData($mediaAttributeCode);
                }

                if ($fileLabel && $addedFile) {
                    $this->_galleryBackendModel->updateImage($product, $addedFile, ['label' => $fileLabel]);
                }
            }
        }

        $product->setIsMassupdate(true);
        $product->setExcludeUrlRewrite(true);

        $product->save();

        // Store affected products ids
        $this->_addAffectedEntityIds($product->getId());

        return true;
    }

    /**
     * Silently save product (import)
     *
     * @return bool
     */
    public function saveRowSilently(array $importData)
    {
        try {
            return $this->saveRow($importData);
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Process after import data
     * Init indexing process after catalog product import
     */
    public function finish()
    {
        /**
         * Back compatibility event
         */
        Mage::dispatchEvent($this->_eventPrefix . '_after', []);

        $entity = new Varien_Object();
        Mage::getSingleton('index/indexer')->processEntityAction(
            $entity,
            self::ENTITY,
            Mage_Index_Model_Event::TYPE_SAVE,
        );
    }
}
