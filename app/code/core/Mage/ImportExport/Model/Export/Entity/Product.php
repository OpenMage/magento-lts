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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Export entity product model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Export_Entity_Product extends Mage_ImportExport_Model_Export_Entity_Abstract
{
    const CONFIG_KEY_PRODUCT_TYPES = 'global/importexport/export_product_types';

    /**
     * Value that means all entities (e.g. websites, groups etc.)
     */
    const VALUE_ALL = 'all';

    /**
     * Permanent column names.
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COL_STORE    = '_store';
    const COL_ATTR_SET = '_attribute_set';
    const COL_TYPE     = '_type';
    const COL_CATEGORY = '_category';
    const COL_SKU      = 'sku';

    /**
     * Pairs of attribute set ID-to-name.
     *
     * @var array
     */
    protected $_attrSetIdToName = array();

    /**
     * Categories ID to text-path hash.
     *
     * @var array
     */
    protected $_categories = array();

    /**
     * Attributes with index (not label) value.
     *
     * @var array
     */
    protected $_indexValueAttributes = array(
        'status',
        'tax_class_id',
        'visibility',
        'enable_googlecheckout',
        'gift_message_available',
        'custom_design'
    );

    /**
     * Permanent entity columns.
     *
     * @var array
     */
    protected $_permanentAttributes = array(self::COL_SKU);

    /**
     * Array of supported product types as keys with appropriate model object as value.
     *
     * @var array
     */
    protected $_productTypeModels = array();

    /**
     * Array of pairs store ID to its code.
     *
     * @var array
     */
    protected $_storeIdToCode = array();

    /**
     * Website ID-to-code.
     *
     * @var array
     */
    protected $_websiteIdToCode = array();

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->_initTypeModels()
                ->_initAttrValues()
                ->_initStores()
                ->_initAttributeSets()
                ->_initWebsites()
                ->_initCategories();
    }

    /**
     * Initialize attribute sets code-to-id pairs.
     *
     * @return Mage_ImportExport_Model_Export_Entity_Product
     */
    protected function _initAttributeSets()
    {
        $productTypeId = Mage::getModel('catalog/product')->getResource()->getTypeId();
        foreach (Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter($productTypeId) as $attributeSet) {
            $this->_attrSetIdToName[$attributeSet->getId()] = $attributeSet->getAttributeSetName();
        }
        return $this;
    }

    /**
     * Initialize categories ID to text-path hash.
     *
     * @return Mage_ImportExport_Model_Export_Entity_Product
     */
    protected function _initCategories()
    {
        $collection = Mage::getResourceModel('catalog/category_collection')->addNameToResult();
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
        foreach ($collection as $category) {
            $structure = preg_split('#/+#', $category->getPath());
            $pathSize  = count($structure);
            if ($pathSize > 2) {
                $path = array();
                for ($i = 2; $i < $pathSize; $i++) {
                    $path[] = $collection->getItemById($structure[$i])->getName();
                }
                $this->_categories[$category->getId()] = implode('/', $path);
            }
        }
        return $this;
    }

    /**
     * Initialize product type models.
     *
     * @throws Exception
     * @return Mage_ImportExport_Model_Export_Entity_Product
     */
    protected function _initTypeModels()
    {
        $config = Mage::getConfig()->getNode(self::CONFIG_KEY_PRODUCT_TYPES)->asCanonicalArray();
        foreach ($config as $type => $typeModel) {
            if (!($model = Mage::getModel($typeModel, array($this, $type)))) {
                Mage::throwException("Entity type model '{$typeModel}' is not found");
            }
            if (! $model instanceof Mage_ImportExport_Model_Export_Entity_Product_Type_Abstract) {
                Mage::throwException(
                    Mage::helper('importexport')->__('Entity type model must be an instance of Mage_ImportExport_Model_Export_Entity_Product_Type_Abstract')
                );
            }
            if ($model->isSuitable()) {
                $this->_productTypeModels[$type] = $model;
                $this->_disabledAttrs            = array_merge($this->_disabledAttrs, $model->getDisabledAttrs());
                $this->_indexValueAttributes     = array_merge(
                    $this->_indexValueAttributes, $model->getIndexValueAttributes()
                );
            }
        }
        if (!$this->_productTypeModels) {
            Mage::throwException(Mage::helper('importexport')->__('There are no product types available for export'));
        }
        $this->_disabledAttrs = array_unique($this->_disabledAttrs);

        return $this;
    }

    /**
     * Initialize website values.
     *
     * @return Mage_ImportExport_Model_Export_Entity_Product
     */
    protected function _initWebsites()
    {
        /** @var $website Mage_Core_Model_Website */
        foreach (Mage::app()->getWebsites() as $website) {
            $this->_websiteIdToCode[$website->getId()] = $website->getCode();
        }
        return $this;
    }

    /**
     * Prepare products tier prices
     *
     * @param  array $productIds
     * @return array
     */
    protected function _prepareTierPrices(array $productIds)
    {
        $resource = Mage::getSingleton('core/resource');
        $select = $this->_connection->select()
            ->from($resource->getTableName('catalog/product_attribute_tier_price'))
            ->where('entity_id IN(?)', $productIds);

        $rowTierPrices = array();
        $stmt = $this->_connection->query($select);
        while ($tierRow = $stmt->fetch()) {
            $rowTierPrices[$tierRow['entity_id']][] = array(
                '_tier_price_customer_group' => $tierRow['all_groups']
                                                ? self::VALUE_ALL : $tierRow['customer_group_id'],
                '_tier_price_website'        => 0 == $tierRow['website_id']
                                                ? self::VALUE_ALL
                                                : $this->_websiteIdToCode[$tierRow['website_id']],
                '_tier_price_qty'            => $tierRow['qty'],
                '_tier_price_price'          => $tierRow['value']
            );
        }

        return $rowTierPrices;
    }

    /**
     * Prepare catalog inventory
     *
     * @param  array $productIds
     * @return array
     */
    protected function _prepareCatalogInventory(array $productIds)
    {
        $select = $this->_connection->select()
            ->from(Mage::getResourceModel('cataloginventory/stock_item')->getMainTable())
            ->where('product_id IN (?)', $productIds);

        $stmt = $this->_connection->query($select);
        $stockItemRows = array();
        while ($stockItemRow = $stmt->fetch()) {
            $productId = $stockItemRow['product_id'];
            unset(
                $stockItemRow['item_id'], $stockItemRow['product_id'], $stockItemRow['low_stock_date'],
                $stockItemRow['stock_id'], $stockItemRow['stock_status_changed_automatically']
            );
            $stockItemRows[$productId] = $stockItemRow;
        }
        return $stockItemRows;
    }

    /**
     * Prepare product links
     *
     * @param  array $productIds
     * @return array
     */
    protected function _prepareLinks(array $productIds)
    {
        $resource = Mage::getSingleton('core/resource');
        $select = $this->_connection->select()
            ->from(
                array('cpl' => $resource->getTableName('catalog/product_link')),
                array(
                    'cpl.product_id', 'cpe.sku', 'cpl.link_type_id',
                    'position' => 'cplai.value', 'default_qty' => 'cplad.value'
                )
            )
            ->joinLeft(
                array('cpe' => $resource->getTableName('catalog/product')),
                '(cpe.entity_id = cpl.linked_product_id)',
                array()
            )
            ->joinLeft(
                array('cpla' => $resource->getTableName('catalog/product_link_attribute')),
                '(cpla.link_type_id = cpl.link_type_id AND cpla.product_link_attribute_code = "position")',
                array()
            )
            ->joinLeft(
                array('cplaq' => $resource->getTableName('catalog/product_link_attribute')),
                '(cplaq.link_type_id = cpl.link_type_id AND cplaq.product_link_attribute_code = "qty")',
                array()
            )
            ->joinLeft(
                array('cplai' => $resource->getTableName('catalog/product_link_attribute_int')),
                '(cplai.link_id = cpl.link_id AND cplai.product_link_attribute_id = cpla.product_link_attribute_id)',
                array()
            )
            ->joinLeft(
                array('cplad' => $resource->getTableName('catalog/product_link_attribute_decimal')),
                '(cplad.link_id = cpl.link_id AND cplad.product_link_attribute_id = cplaq.product_link_attribute_id)',
                array()
            )
            ->where('cpl.link_type_id IN (?)', array(
                Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED,
                Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL,
                Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL,
                Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED
            ))
            ->where('cpl.product_id IN (?)', $productIds);

        $stmt = $this->_connection->query($select);
        $linksRows = array();
        while ($linksRow = $stmt->fetch()) {
            $linksRows[$linksRow['product_id']][$linksRow['link_type_id']][] = array(
                'sku'         => $linksRow['sku'],
                'position'    => $linksRow['position'],
                'default_qty' => $linksRow['default_qty']
            );
        }

        return $linksRows;
    }

    /**
     * Prepare configurable product data
     *
     * @param  array $productIds
     * @return array
     */
    protected function _prepareConfigurableProductData(array $productIds)
    {
        $resource = Mage::getSingleton('core/resource');
        $select = $this->_connection->select()
            ->from(
                array('cpsl' => $resource->getTableName('catalog/product_super_link')),
                array('cpsl.parent_id', 'cpe.sku')
            )
            ->joinLeft(
                array('cpe' => $resource->getTableName('catalog/product')),
                '(cpe.entity_id = cpsl.product_id)',
                array()
            )
            ->where('parent_id IN (?)', $productIds);
        $stmt = $this->_connection->query($select);
        $configurableData = array();
        while ($cfgLinkRow = $stmt->fetch()) {
            $configurableData[$cfgLinkRow['parent_id']][] = array('_super_products_sku' => $cfgLinkRow['sku']);
        }

        return $configurableData;
    }

    /**
     * Prepare configurable product price
     *
     * @param  array $productIds
     * @return array
     */
    protected function _prepareConfigurableProductPrice(array $productIds)
    {
        $resource = Mage::getSingleton('core/resource');
        $select = $this->_connection->select()
            ->from(
                array('cpsa' => $resource->getTableName('catalog/product_super_attribute')),
                array(
                    'cpsa.product_id', 'ea.attribute_code', 'eaov.value', 'cpsap.pricing_value', 'cpsap.is_percent'
                )
            )
            ->joinLeft(
                array('cpsap' => $resource->getTableName('catalog/product_super_attribute_pricing')),
                '(cpsap.product_super_attribute_id = cpsa.product_super_attribute_id)',
                array()
            )
            ->joinLeft(
                array('ea' => $resource->getTableName('eav/attribute')),
                '(ea.attribute_id = cpsa.attribute_id)',
                array()
            )
            ->joinLeft(
                array('eaov' => $resource->getTableName('eav/attribute_option_value')),
                '(eaov.option_id = cpsap.value_index AND store_id = 0)',
                array()
            )
            ->where('cpsa.product_id IN (?)', $productIds);
        $configurablePrice = array();
        $stmt = $this->_connection->query($select);
        while ($priceRow = $stmt->fetch()) {
            $configurablePrice[$priceRow['product_id']][] = array(
                '_super_attribute_code'       => $priceRow['attribute_code'],
                '_super_attribute_option'     => $priceRow['value'],
                '_super_attribute_price_corr' => $priceRow['pricing_value'] . ($priceRow['is_percent'] ? '%' : '')
            );
        }
        return $configurablePrice;
    }

    /**
     * Export process.
     *
     * @return string
     */
    public function export()
    {
        /** @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
        $validAttrCodes  = $this->_getExportAttrCodes();
        $writer          = $this->getWriter();
        $resource        = Mage::getSingleton('core/resource');
        $dataRows        = array();
        $rowCategories   = array();
        $rowWebsites     = array();
        $rowTierPrices   = array();
        $stockItemRows   = array();
        $linksRows       = array();
        $gfAmountFields  = array();
        $defaultStoreId  = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
        $collection = $this->_prepareEntityCollection(Mage::getResourceModel('catalog/product_collection'));

        // prepare multi-store values and system columns values
        foreach ($this->_storeIdToCode as $storeId => &$storeCode) { // go through all stores
            $collection->setStoreId($storeId)
                ->load();

            if ($defaultStoreId == $storeId) {
                $collection->addCategoryIds()->addWebsiteNamesToResult();

                // tier price data getting only once
                $rowTierPrices = $this->_prepareTierPrices($collection->getAllIds());
            }
            foreach ($collection as $itemId => $item) { // go through all products
                $rowIsEmpty = true; // row is empty by default

                foreach ($validAttrCodes as &$attrCode) { // go through all valid attribute codes
                    $attrValue = $item->getData($attrCode);

                    if (!empty($this->_attributeValues[$attrCode])) {
                        if (isset($this->_attributeValues[$attrCode][$attrValue])) {
                            $attrValue = $this->_attributeValues[$attrCode][$attrValue];
                        } else {
                            $attrValue = null;
                        }
                    }
                    // do not save value same as default or not existent
                    if ($storeId != $defaultStoreId
                        && isset($dataRows[$itemId][$defaultStoreId][$attrCode])
                        && $dataRows[$itemId][$defaultStoreId][$attrCode] == $attrValue
                    ) {
                        $attrValue = null;
                    }
                    if (is_scalar($attrValue)) {
                        $dataRows[$itemId][$storeId][$attrCode] = $attrValue;
                        $rowIsEmpty = false; // mark row as not empty
                    }
                }
                if ($rowIsEmpty) { // remove empty rows
                    unset($dataRows[$itemId][$storeId]);
                } else {
                    $attrSetId = $item->getAttributeSetId();
                    $dataRows[$itemId][$storeId][self::COL_STORE]    = $storeCode;
                    $dataRows[$itemId][$storeId][self::COL_ATTR_SET] = $this->_attrSetIdToName[$attrSetId];
                    $dataRows[$itemId][$storeId][self::COL_TYPE]     = $item->getTypeId();

                    if ($defaultStoreId == $storeId) {
                        $rowWebsites[$itemId]   = $item->getWebsites();
                        $rowCategories[$itemId] = $item->getCategoryIds();
                    }
                }
                $item = null;
            }
            $collection->clear();
        }

        // remove root categories
        foreach ($rowCategories as $productId => &$categories) {
            $categories = array_intersect($categories, array_keys($this->_categories));
        }

        // prepare catalog inventory information
        $productIds = array_keys($dataRows);
        $stockItemRows = $this->_prepareCatalogInventory($productIds);

        // prepare links information
        $this->_prepareLinks($productIds);
        $linkIdColPrefix = array(
            Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED   => '_links_related_',
            Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL    => '_links_upsell_',
            Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL => '_links_crosssell_',
            Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED   => '_associated_'
        );

        // prepare configurable products data
        $configurableData  = $this->_prepareConfigurableProductData($productIds);
        $configurablePrice = array();
        if ($configurableData) {
            $configurablePrice = $this->_prepareConfigurableProductPrice($productIds);
            foreach ($configurableData as $productId => &$rows) {
                if (isset($configurablePrice[$productId])) {
                    $largest = max(count($rows), count($configurablePrice[$productId]));

                    for ($i = 0; $i < $largest; $i++) {
                        if (!isset($configurableData[$productId][$i])) {
                            $configurableData[$productId][$i] = array();
                        }
                        if (isset($configurablePrice[$productId][$i])) {
                            $configurableData[$productId][$i] = array_merge(
                                $configurableData[$productId][$i],
                                $configurablePrice[$productId][$i]
                            );
                        }
                    }
                }
            }
            unset($configurablePrice);
        }

        // prepare custom options information
        $customOptionsData    = array();
        $customOptionsDataPre = array();
        $customOptCols        = array(
            '_custom_option_store', '_custom_option_type', '_custom_option_title', '_custom_option_is_required',
            '_custom_option_price', '_custom_option_sku', '_custom_option_max_characters',
            '_custom_option_sort_order', '_custom_option_row_title', '_custom_option_row_price',
            '_custom_option_row_sku', '_custom_option_row_sort'
        );

        foreach ($this->_storeIdToCode as $storeId => &$storeCode) {
            $options = Mage::getResourceModel('catalog/product_option_collection')
                ->reset()
                ->addTitleToResult($storeId)
                ->addPriceToResult($storeId)
                ->addProductToFilter($productIds)
                ->addValuesToResult($storeId);

            foreach ($options as $option) {
                $row = array();
                $productId = $option['product_id'];
                $optionId  = $option['option_id'];
                $customOptions = isset($customOptionsDataPre[$productId][$optionId])
                               ? $customOptionsDataPre[$productId][$optionId]
                               : array();

                if ($defaultStoreId == $storeId) {
                    $row['_custom_option_type']           = $option['type'];
                    $row['_custom_option_title']          = $option['title'];
                    $row['_custom_option_is_required']    = $option['is_require'];
                    $row['_custom_option_price'] = $option['price'] . ($option['price_type'] == 'percent' ? '%' : '');
                    $row['_custom_option_sku']            = $option['sku'];
                    $row['_custom_option_max_characters'] = $option['max_characters'];
                    $row['_custom_option_sort_order']     = $option['sort_order'];

                    // remember default title for later comparisons
                    $defaultTitles[$option['option_id']] = $option['title'];
                } elseif ($option['title'] != $customOptions[0]['_custom_option_title']) {
                    $row['_custom_option_title'] = $option['title'];
                }
                if ($values = $option->getValues()) {
                    $firstValue = array_shift($values);
                    $priceType  = $firstValue['price_type'] == 'percent' ? '%' : '';

                    if ($defaultStoreId == $storeId) {
                        $row['_custom_option_row_title'] = $firstValue['title'];
                        $row['_custom_option_row_price'] = $firstValue['price'] . $priceType;
                        $row['_custom_option_row_sku']   = $firstValue['sku'];
                        $row['_custom_option_row_sort']  = $firstValue['sort_order'];

                        $defaultValueTitles[$firstValue['option_type_id']] = $firstValue['title'];
                    } elseif ($firstValue['title'] != $customOptions[0]['_custom_option_row_title']) {
                        $row['_custom_option_row_title'] = $firstValue['title'];
                    }
                }
                if ($row) {
                    if ($defaultStoreId != $storeId) {
                        $row['_custom_option_store'] = $this->_storeIdToCode[$storeId];
                    }
                    $customOptionsDataPre[$productId][$optionId][] = $row;
                }
                foreach ($values as $value) {
                    $row = array();
                    $valuePriceType = $value['price_type'] == 'percent' ? '%' : '';

                    if ($defaultStoreId == $storeId) {
                        $row['_custom_option_row_title'] = $value['title'];
                        $row['_custom_option_row_price'] = $value['price'] . $valuePriceType;
                        $row['_custom_option_row_sku']   = $value['sku'];
                        $row['_custom_option_row_sort']  = $value['sort_order'];
                    } elseif ($value['title'] != $customOptions[0]['_custom_option_row_title']) {
                        $row['_custom_option_row_title'] = $value['title'];
                    }
                    if ($row) {
                        if ($defaultStoreId != $storeId) {
                            $row['_custom_option_store'] = $this->_storeIdToCode[$storeId];
                        }
                        $customOptionsDataPre[$option['product_id']][$option['option_id']][] = $row;
                    }
                }
                $option = null;
            }
            $options = null;
        }
        foreach ($customOptionsDataPre as $productId => &$optionsData) {
            $customOptionsData[$productId] = array();

            foreach ($optionsData as $optionId => &$optionRows) {
                $customOptionsData[$productId] = array_merge($customOptionsData[$productId], $optionRows);
            }
            unset($optionRows, $optionsData);
        }
        unset($customOptionsDataPre);

        // create export file
        $headerCols = array_merge(
            array(
                self::COL_SKU, self::COL_STORE, self::COL_ATTR_SET,
                self::COL_TYPE, self::COL_CATEGORY, '_product_websites'
            ),
            $validAttrCodes,
            reset($stockItemRows) ? array_keys(end($stockItemRows)) : array(),
            $gfAmountFields,
            array(
                '_links_related_sku', '_links_related_position', '_links_crosssell_sku',
                '_links_crosssell_position', '_links_upsell_sku', '_links_upsell_position',
                '_associated_sku', '_associated_default_qty', '_associated_position'
            ),
            array('_tier_price_website', '_tier_price_customer_group', '_tier_price_qty', '_tier_price_price')
        );

        // have we merge custom options columns
        if ($customOptionsData) {
            $headerCols = array_merge($headerCols, $customOptCols);
        }

        // have we merge configurable products data
        if ($configurableData) {
            $headerCols = array_merge($headerCols, array(
                '_super_products_sku', '_super_attribute_code',
                '_super_attribute_option', '_super_attribute_price_corr'
            ));
        }

        $writer->setHeaderCols($headerCols);

        foreach ($dataRows as $productId => &$productData) {
            foreach ($productData as $storeId => &$dataRow) {
                if ($defaultStoreId != $storeId) {
                    $dataRow[self::COL_SKU]      = null;
                    $dataRow[self::COL_ATTR_SET] = null;
                    $dataRow[self::COL_TYPE]     = null;
                } else {
                    $dataRow[self::COL_STORE] = null;
                    $dataRow += $stockItemRows[$productId];
                }
                if ($rowCategories[$productId]) {
                    $dataRow[self::COL_CATEGORY] = $this->_categories[array_shift($rowCategories[$productId])];
                }
                if ($rowWebsites[$productId]) {
                    $dataRow['_product_websites'] = $this->_websiteIdToCode[array_shift($rowWebsites[$productId])];
                }
                if (!empty($rowTierPrices[$productId])) {
                    $dataRow = array_merge($dataRow, array_shift($rowTierPrices[$productId]));
                }
                foreach ($linkIdColPrefix as $linkId => &$colPrefix) {
                    if (!empty($linksRows[$productId][$linkId])) {
                        $linkData = array_shift($linksRows[$productId][$linkId]);
                        $dataRow[$colPrefix . 'position'] = $linkData['position'];
                        $dataRow[$colPrefix . 'sku'] = $linkData['sku'];

                        if (null !== $linkData['default_qty']) {
                            $dataRow[$colPrefix . 'default_qty'] = $linkData['default_qty'];
                        }
                    }
                }
                if (!empty($customOptionsData[$productId])) {
                    $dataRow = array_merge($dataRow, array_shift($customOptionsData[$productId]));
                }
                if (!empty($configurableData[$productId])) {
                    $dataRow = array_merge($dataRow, array_shift($configurableData[$productId]));
                }

                $writer->writeRow($dataRow);
            }
            // calculate largest links block
            $largestLinks = 0;

            if (isset($linksRows[$productId])) {
                foreach ($linksRows[$productId] as &$linkData) {
                    $largestLinks = max($largestLinks, count($linkData));
                }
            }
            $additionalRowsCount = max(
                count($rowCategories[$productId]),
                count($rowWebsites[$productId]),
                $largestLinks
            );
            if (!empty($rowTierPrices[$productId])) {
                $additionalRowsCount = max($additionalRowsCount, count($rowTierPrices[$productId]));
            }
            if (!empty($customOptionsData[$productId])) {
                $additionalRowsCount = max($additionalRowsCount, count($customOptionsData[$productId]));
            }
            if (!empty($configurableData[$productId])) {
                $additionalRowsCount = max($additionalRowsCount, count($configurableData[$productId]));
            }

            if ($additionalRowsCount) {
                for ($i = 0; $i < $additionalRowsCount; $i++) {
                    $dataRow = array();

                    if ($rowCategories[$productId]) {
                        $dataRow[self::COL_CATEGORY] = $this->_categories[array_shift($rowCategories[$productId])];
                    }
                    if ($rowWebsites[$productId]) {
                        $dataRow['_product_websites'] = $this->_websiteIdToCode[array_shift($rowWebsites[$productId])];
                    }
                    if (!empty($rowTierPrices[$productId])) {
                        $dataRow = array_merge($dataRow, array_shift($rowTierPrices[$productId]));
                    }
                    foreach ($linkIdColPrefix as $linkId => &$colPrefix) {
                        if (!empty($linksRows[$productId][$linkId])) {
                            $linkData = array_shift($linksRows[$productId][$linkId]);
                            $dataRow[$colPrefix . 'position'] = $linkData['position'];
                            $dataRow[$colPrefix . 'sku'] = $linkData['sku'];

                            if (null !== $linkData['default_qty']) {
                                $dataRow[$colPrefix . 'default_qty'] = $linkData['default_qty'];
                            }
                        }
                    }
                    if (!empty($customOptionsData[$productId])) {
                        $dataRow = array_merge($dataRow, array_shift($customOptionsData[$productId]));
                    }
                    if (!empty($configurableData[$productId])) {
                        $dataRow = array_merge($dataRow, array_shift($configurableData[$productId]));
                    }
                    $writer->writeRow($dataRow);
                }
            }
        }
        return $writer->getContents();
    }

    /**
     * Clean up already loaded attribute collection.
     *
     * @param Mage_Eav_Model_Mysql4_Entity_Attribute_Collection $collection
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function filterAttributeCollection(Mage_Eav_Model_Mysql4_Entity_Attribute_Collection $collection)
    {
        $validTypes = array_keys($this->_productTypeModels);

        foreach (parent::filterAttributeCollection($collection) as $attribute) {
            $attrApplyTo = $attribute->getApplyTo();
            $attrApplyTo = $attrApplyTo ? array_intersect($attrApplyTo, $validTypes) : $validTypes;

            if ($attrApplyTo) {
                foreach ($attrApplyTo as $productType) { // override attributes by its product type model
                    if ($this->_productTypeModels[$productType]->overrideAttribute($attribute)) {
                        break;
                    }
                }
            } else { // remove attributes of not-supported product types
                $collection->removeItemByKey($attribute->getId());
            }
        }
        return $collection;
    }

    /**
     * Entity attributes collection getter.
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
     */
    public function getAttributeCollection()
    {
        return Mage::getResourceModel('catalog/product_attribute_collection');
    }

    /**
     * EAV entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'catalog_product';
    }
}
