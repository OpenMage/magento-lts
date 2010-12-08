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
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle Type Model
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Model_Product_Type extends Mage_Catalog_Model_Product_Type_Abstract
{
    /**
     * Product is composite
     *
     * @var bool
     */
    protected $_isComposite = true;

    /**
     * Cache key for Options Collection
     *
     * @var string
     */
    protected $_keyOptionsCollection        = '_cache_instance_options_collection';

    /**
     * Cache key for Selections Collection
     *
     * @var string
     */
    protected $_keySelectionsCollection     = '_cache_instance_selections_collection';

    /**
     * Cache key for used Selections
     *
     * @var string
     */
    protected $_keyUsedSelections           = '_cache_instance_used_selections';

    /**
     * Cache key for used selections ids
     *
     * @var string
     */
    protected $_keyUsedSelectionsIds        = '_cache_instance_used_selections_ids';

    /**
     * Cache key for used options
     *
     * @var string
     */
    protected $_keyUsedOptions              = '_cache_instance_used_options';

    /**
     * Cache key for used options ids
     *
     * @var string
     */
    protected $_keyUsedOptionsIds           = '_cache_instance_used_options_ids';

    /**
     * Return relation info about used products
     *
     * @return Varien_Object Object with information data
     */
    public function getRelationInfo()
    {
        $info = new Varien_Object();
        $info->setTable('bundle/selection')
            ->setParentFieldName('parent_product_id')
            ->setChildFieldName('product_id');
        return $info;
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param int $parentId
     * @param bool $required
     * @return array
     */
    public function getChildrenIds($parentId, $required = true)
    {
        return Mage::getResourceSingleton('bundle/selection')
            ->getChildrenIds($parentId, $required);
    }

    /**
     * Retrieve parent ids array by requered child
     *
     * @param int|array $childId
     * @return array
     */
    public function getParentIdsByChild($childId)
    {
        return Mage::getResourceSingleton('bundle/selection')
            ->getParentIdsByChild($childId);
    }

    /**
     * Return product sku based on sku_type attribute
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getSku($product = null)
    {
        $sku = parent::getSku($product);

        if ($this->getProduct($product)->getData('sku_type')) {
            return $sku;
        } else {
            $skuParts = array($sku);

            if ($this->getProduct($product)->hasCustomOptions()) {
                $customOption = $this->getProduct($product)->getCustomOption('bundle_selection_ids');
                $selectionIds = unserialize($customOption->getValue());
                $selections = $this->getSelectionsByIds($selectionIds, $product);
                foreach ($selections->getItems() as $selection) {
                    $skuParts[] = $selection->getSku();
                }
            }

            return implode('-', $skuParts);
        }
    }

    /**
     * Return product weight based on weight_type attribute
     *
     * @param Mage_Catalog_Model_Product $product
     * @return decimal
     */
    public function getWeight($product = null)
    {
        if ($this->getProduct($product)->getData('weight_type')) {
            return $this->getProduct($product)->getData('weight');
        } else {
            $weight = 0;

            if ($this->getProduct($product)->hasCustomOptions()) {
                $customOption = $this->getProduct($product)->getCustomOption('bundle_selection_ids');
                $selectionIds = unserialize($customOption->getValue());
                $selections = $this->getSelectionsByIds($selectionIds, $product);
                foreach ($selections->getItems() as $selection) {
                    $weight += $selection->getWeight();
                }
            }
            return $weight;
        }
    }

    /**
     * Check is virtual product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isVirtual($product = null)
    {
        if ($this->getProduct($product)->hasCustomOptions()) {
            $customOption = $this->getProduct($product)->getCustomOption('bundle_selection_ids');
            $selectionIds = unserialize($customOption->getValue());
            $selections = $this->getSelectionsByIds($selectionIds, $product);
            $virtualCount = 0;
            foreach ($selections->getItems() as $selection) {
                if ($selection->isVirtual()) {
                    $virtualCount++;
                }
            }
            if ($virtualCount == count($selections)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Before save type related data
     *
     * @param Mage_Catalog_Model_Product $product
     */
    public function beforeSave($product = null)
    {
        parent::beforeSave($product);
        $product = $this->getProduct($product);

        // If bundle product has dynamic weight, than delete weight attribute
        if (!$product->getData('weight_type') && $product->hasData('weight')) {
            $product->setData('weight', false);
        }

        $product->canAffectOptions(false);

        if ($product->getCanSaveBundleSelections()) {
            $product->canAffectOptions(true);
            $selections = $product->getBundleSelectionsData();
            if ($selections) {
                if (!empty($selections)) {
                    $options = $product->getBundleOptionsData();
                    if ($options) {
                        foreach ($options as $option) {
                            if (empty($option['delete']) || 1 != (int)$option['delete']) {
                                $product->setTypeHasOptions(true);
                                if (1 == (int)$option['required']) {
                                    $product->setTypeHasRequiredOptions(true);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Save type related data
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Product_Type
     */
    public function save($product = null)
    {
        parent::save($product);
        /* @var $resource Mage_Bundle_Model_Mysql4_Bundle */
        $resource = Mage::getResourceModel('bundle/bundle');

        $options = $this->getProduct($product)->getBundleOptionsData();
        if ($options) {
            $this->getProduct($product)->setIsRelationsChanged(true);

            foreach ($options as $key => $option) {
                if (isset($option['option_id']) && $option['option_id'] == '') {
                    unset($option['option_id']);
                }

                $optionModel = Mage::getModel('bundle/option')
                    ->setData($option)
                    ->setParentId($this->getProduct($product)->getId())
                    ->setStoreId($this->getProduct($product)->getStoreId());

                $optionModel->isDeleted((bool)$option['delete']);
                $optionModel->save();

                $options[$key]['option_id'] = $optionModel->getOptionId();
            }

            $usedProductIds      = array();
            $excludeSelectionIds = array();

            $selections = $this->getProduct($product)->getBundleSelectionsData();
            if ($selections) {
                foreach ($selections as $index => $group) {
                    foreach ($group as $key => $selection) {
                        if (isset($selection['selection_id']) && $selection['selection_id'] == '') {
                            unset($selection['selection_id']);
                        }

                        if (!isset($selection['is_default'])) {
                            $selection['is_default'] = 0;
                        }

                        $selectionModel = Mage::getModel('bundle/selection')
                            ->setData($selection)
                            ->setOptionId($options[$index]['option_id'])
                            ->setParentProductId($this->getProduct($product)->getId());

                        $selectionModel->isDeleted((bool)$selection['delete']);
                        $selectionModel->save();

                        $selection['selection_id'] = $selectionModel->getSelectionId();

                        if ($selectionModel->getSelectionId()) {
                            $excludeSelectionIds[] = $selectionModel->getSelectionId();
                            $usedProductIds[] = $selectionModel->getProductId();
                        }
                    }
                }

                $resource->dropAllUnneededSelections($this->getProduct($product)->getId(), $excludeSelectionIds);
                $resource->saveProductRelations($this->getProduct($product)->getId(), array_unique($usedProductIds));
            }

            if ($this->getProduct($product)->getData('price_type') != $this->getProduct($product)->getOrigData('price_type')) {
                $resource->dropAllQuoteChildItems($this->getProduct($product)->getId());
            }
        }

        return $this;
    }

    /**
     * Retrieve bundle options items
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getOptions($product = null)
    {
        return $this->getOptionsCollection($product)->getItems();
    }

    /**
     * Retrieve bundle options ids
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getOptionsIds($product = null)
    {
        return $this->getOptionsCollection($product)->getAllIds();
    }

    /**
     * Retrieve bundle option collection
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Mysql4_Option_Collection
     */
    public function getOptionsCollection($product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_keyOptionsCollection)) {
            $optionsCollection = Mage::getModel('bundle/option')->getResourceCollection()
                ->setProductIdFilter($this->getProduct($product)->getId())
                ->setPositionOrder()
                ->joinValues($this->getStoreFilter($product));
            $this->getProduct($product)->setData($this->_keyOptionsCollection, $optionsCollection);
        }
        return $this->getProduct($product)->getData($this->_keyOptionsCollection);
    }

    /**
     * Retrive bundle selections collection based on used options
     *
     * @param array $optionIds
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Mysql4_Selection_Collection
     */
    public function getSelectionsCollection($optionIds, $product = null)
    {
        $keyOptionIds = (is_array($optionIds) ? implode('_', $optionIds) : '');
        $key = $this->_keySelectionsCollection . $keyOptionIds;
        if (!$this->getProduct($product)->hasData($key)) {
            $storeId = $this->getProduct($product)->getStoreId();
            $selectionsCollection = Mage::getResourceModel('bundle/selection_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->setFlag('require_stock_items', true)
                ->setFlag('product_children', true)
                ->setPositionOrder()
                ->addStoreFilter($this->getStoreFilter($product))
                ->setStoreId($storeId)
                ->addFilterByRequiredOptions()
                ->setOptionIdsFilter($optionIds);

            if (!Mage::helper('catalog')->isPriceGlobal() && $storeId) {
                $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
                $selectionsCollection->joinPrices($websiteId);
            }

            $this->getProduct($product)->setData($key, $selectionsCollection);
        }
        return $this->getProduct($product)->getData($key);
    }

    /**
     * Method is needed for specific actions to change given quote options values
     * according current product type logic
     * Example: the cataloginventory validation of decimal qty can change qty to int,
     * so need to change quote item qty option value too.
     *
     * @param   array           $options
     * @param   Varien_Object   $option
     * @param   mixed           $value
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Bundle_Model_Product_Type
     */
    public function updateQtyOption($options, Varien_Object $option, $value, $product = null)
    {
        $optionProduct      = $option->getProduct($product);
        $optionUpdateFlag   = $option->getHasQtyOptionUpdate();
        $optionCollection   = $this->getOptionsCollection($product);

        $selections = $this->getSelectionsCollection($optionCollection->getAllIds(), $product);

        foreach ($selections as $selection) {
            if ($selection->getProductId() == $optionProduct->getId()) {
                foreach ($options as &$option) {
                    if ($option->getCode() == 'selection_qty_'.$selection->getSelectionId()) {
                        if ($optionUpdateFlag) {
                            $option->setValue(intval($option->getValue()));
                        }
                        else {
                            $option->setValue($value);
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Prepare Quote Item Quantity
     *
     * @param mixed $qty
     * @param Mage_Catalog_Model_Product $product
     * @return int
     */
    public function prepareQuoteItemQty($qty, $product = null)
    {
        return intval($qty);
    }

    /**
     * Checking if we can sale this bundle
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isSalable($product = null)
    {
        $salable = parent::isSalable($product);
        if (!is_null($salable)) {
            return $salable;
        }

        $optionCollection = $this->getOptionsCollection($product);

        if (!count($optionCollection->getItems())) {
            return false;
        }

        $requiredOptionIds = array();

        foreach ($optionCollection->getItems() as $option) {
            if ($option->getRequired()) {
                $requiredOptionIds[$option->getId()] = 0;
            }
        }

        $selectionCollection = $this->getSelectionsCollection($optionCollection->getAllIds(), $product);

        if (!count($selectionCollection->getItems())) {
            return false;
        }
        $salableSelectionCount = 0;
        foreach ($selectionCollection as $selection) {
            if ($selection->isSalable()) {
                $requiredOptionIds[$selection->getOptionId()] = 1;
                $salableSelectionCount++;
            }

        }

        return (array_sum($requiredOptionIds) == count($requiredOptionIds) && $salableSelectionCount);
    }

    /**
     * Initialize product(s) for add to cart process
     *
     * @param   Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @return  unknown
     */
    public function prepareForCart(Varien_Object $buyRequest, $product = null)
    {
        $result = parent::prepareForCart($buyRequest, $product);

        if (is_string($result)) {
            return $result;
        }

        $selections = array();

        $product = $this->getProduct($product);

        $_appendAllSelections = false;
        if ($product->getSkipCheckRequiredOption()) {
            $_appendAllSelections = true;
        }

        $options = array_filter($buyRequest->getBundleOption(), 'intval');

        if ($options) {
            $qtys = $buyRequest->getBundleOptionQty();
            foreach ($options as $_optionId => $_selections) {
                if (empty($_selections)) {
                    unset($options[$_optionId]);
                }
            }
            $optionIds = array_keys($options);

            if (empty($optionIds)) {
                return Mage::helper('bundle')->__('Please select options for product.');
            }

            //$optionsCollection = $this->getOptionsByIds($optionIds, $product);
            $product->getTypeInstance(true)->setStoreFilter($product->getStoreId(), $product);
            $optionsCollection = $this->getOptionsCollection($product);
            if (!$this->getProduct($product)->getSkipCheckRequiredOption()) {
                foreach ($optionsCollection->getItems() as $option) {
                    if ($option->getRequired() && !isset($options[$option->getId()])) {
                        return Mage::helper('bundle')->__('Required options are not selected.');
                    }
                }
            }
            $selectionIds = array();

            foreach ($options as $optionId => $selectionId) {
                if (!is_array($selectionId)) {
                    if ($selectionId != '') {
                        $selectionIds[] = (int)$selectionId;
                    }
                } else {
                    foreach ($selectionId as $id) {
                        if ($id != '') {
                            $selectionIds[] = (int)$id;
                        }
                    }
                }
            }

            $selections = $this->getSelectionsByIds($selectionIds, $product);

            /**
             * checking if selections that where added are still on sale
             */
            foreach ($selections->getItems() as $key => $selection) {
                if (!$selection->isSalable()) {
                    $_option = $optionsCollection->getItemById($selection->getOptionId());
                    if (is_array($options[$_option->getId()]) && count($options[$_option->getId()]) > 1){
                        $moreSelections = true;
                    } else {
                        $moreSelections = false;
                    }
                    if ($_option->getRequired() && (!$_option->isMultiSelection() || ($_option->isMultiSelection() && !$moreSelections))) {
                        return Mage::helper('bundle')->__('Selected required options are not available.');
                    }
                }
            }

            $optionsCollection->appendSelections($selections, false, $_appendAllSelections);

            $selections = $selections->getItems();
        } else {
            $product->getTypeInstance(true)->setStoreFilter($product->getStoreId(), $product);

            $optionCollection = $product->getTypeInstance(true)->getOptionsCollection($product);

            $optionIds = $product->getTypeInstance(true)->getOptionsIds($product);
            $selectionIds = array();

            $selectionCollection = $product->getTypeInstance(true)
                ->getSelectionsCollection(
                    $product->getTypeInstance(true)->getOptionsIds($product),
                    $product
                );

            $options = $optionCollection->appendSelections($selectionCollection, false, $_appendAllSelections);

            foreach ($options as $option) {
                if ($option->getRequired() && count($option->getSelections()) == 1) {
                    $selections = array_merge($selections, $option->getSelections());
                } else {
                    $selections = array();
                    break;
                }
            }
        }
        if (count($selections) > 0) {
            $uniqueKey = array($product->getId());
            $selectionIds = array();

            /*
             * shaking selection array :) by option position
             */
            usort($selections, array($this, "shakeSelections"));

            foreach ($selections as $selection) {
                if ($selection->getSelectionCanChangeQty() && isset($qtys[$selection->getOptionId()])) {
                    $qty = $qtys[$selection->getOptionId()] > 0 ? $qtys[$selection->getOptionId()] : 1;
                } else {
                    $qty = $selection->getSelectionQty() ? $selection->getSelectionQty() : 1;
                }

                $product->addCustomOption('selection_qty_' . $selection->getSelectionId(), $qty, $selection);
                $selection->addCustomOption('selection_id', $selection->getSelectionId());

                if ($customOption = $product->getCustomOption('product_qty_' . $selection->getId())) {
                    $customOption->setValue($customOption->getValue() + $qty);
                } else {
                    $product->addCustomOption('product_qty_' . $selection->getId(), $qty, $selection);
                }

                /*
                 * creating extra attributes that will be converted
                 * to product options in order item
                 * for selection (not for all bundle)
                 */
                $price = $product->getPriceModel()->getSelectionPrice($product, $selection, $qty);
                $attributes = array(
                    'price' => Mage::app()->getStore()->convertPrice($price),
                    'qty' => $qty,
                    'option_label' => $selection->getOption()->getTitle(),
                    'option_id' => $selection->getOption()->getId()
                );

                //if (!$product->getPriceType()) {
                $_result = $selection->getTypeInstance(true)->prepareForCart($buyRequest, $selection);
                if (is_string($_result) && !is_array($_result)) {
                    return $_result;
                }

                if (!isset($_result[0])) {
                    return Mage::helper('checkout')->__('Cannot add item to the shopping cart.');
                }

                $result[] = $_result[0]->setParentProductId($product->getId())
                    ->addCustomOption('bundle_option_ids', serialize($optionIds))
                    ->addCustomOption('bundle_selection_attributes', serialize($attributes))
                    ->setCartQty($qty);
                //}

                $selectionIds[] = $_result[0]->getSelectionId();
                $uniqueKey[] = $_result[0]->getSelectionId();
                $uniqueKey[] = $qty;
            }
            /**
             * "unique" key for bundle selection and add it to selections and bundle for selections
             */
            $uniqueKey = implode('_', $uniqueKey);
            foreach ($result as $item) {
                $item->addCustomOption('bundle_identity', $uniqueKey);
            }
            $product->addCustomOption('bundle_option_ids', serialize($optionIds));
            $product->addCustomOption('bundle_selection_ids', serialize($selectionIds));

            return $result;
        }
        return $this->getSpecifyOptionMessage();
    }

    /**
     * Retrieve message for specify option(s)
     *
     * @return string
     */
    public function getSpecifyOptionMessage()
    {
        return Mage::helper('bundle')->__('Please specify product option(s).');
    }

    /**
     * Retrieve bundle selections collection based on ids
     *
     * @param array $selectionIds
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Mysql4_Selection_Collection
     */
    public function getSelectionsByIds($selectionIds, $product = null)
    {
        sort($selectionIds);

        $usedSelections     = $this->getProduct($product)->getData($this->_keyUsedSelections);
        $usedSelectionsIds  = $this->getProduct($product)->getData($this->_keyUsedSelectionsIds);

        if (!$usedSelections || serialize($usedSelectionsIds) != serialize($selectionIds)) {
            $storeId = $this->getProduct($product)->getStoreId();
            $usedSelections = Mage::getResourceModel('bundle/selection_collection')
                ->addAttributeToSelect('*')
                ->setFlag('require_stock_items', true)
                ->addStoreFilter($this->getStoreFilter($product))
                ->setStoreId($storeId)
                ->setPositionOrder()
                ->addFilterByRequiredOptions()
                ->setSelectionIdsFilter($selectionIds);

                if (!Mage::helper('catalog')->isPriceGlobal() && $storeId) {
                    $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
                    $usedSelections->joinPrices($websiteId);
                }
            $this->getProduct($product)->setData($this->_keyUsedSelections, $usedSelections);
            $this->getProduct($product)->setData($this->_keyUsedSelectionsIds, $selectionIds);
        }
        return $usedSelections;
    }

    /**
     * Retrieve bundle options collection based on ids
     *
     * @param array $optionIds
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Mysql4_Option_Collection
     */
    public function getOptionsByIds($optionIds, $product = null)
    {
        sort($optionIds);

        $usedOptions     = $this->getProduct($product)->getData($this->_keyUsedOptions);
        $usedOptionsIds  = $this->getProduct($product)->getData($this->_keyUsedOptionsIds);

        if (!$usedOptions || serialize($usedOptionsIds) != serialize($optionIds)) {
            $usedOptions = Mage::getModel('bundle/option')->getResourceCollection()
                ->setProductIdFilter($this->getProduct($product)->getId())
                ->setPositionOrder()
                ->joinValues(Mage::app()->getStore()->getId())
                ->setIdFilter($optionIds);
            $this->getProduct($product)->setData($this->_keyUsedOptions, $usedOptions);
            $this->getProduct($product)->setData($this->_keyUsedOptionsIds, $optionIds);
        }
        return $usedOptions;
    }

    /**
     * Prepare additional options/information for order item which will be
     * created from this product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getOrderOptions($product = null)
    {
        $optionArr = parent::getOrderOptions($product);

        $bundleOptions = array();

        $product = $this->getProduct($product);

        if ($product->hasCustomOptions()) {
            $customOption = $product->getCustomOption('bundle_option_ids');
            $optionIds = unserialize($customOption->getValue());
            $options = $this->getOptionsByIds($optionIds, $product);
            $customOption = $product->getCustomOption('bundle_selection_ids');
            $selectionIds = unserialize($customOption->getValue());
            $selections = $this->getSelectionsByIds($selectionIds, $product);
            foreach ($selections->getItems() as $selection) {
                if ($selection->isSalable()) {
                    $selectionQty = $product->getCustomOption('selection_qty_' . $selection->getSelectionId());
                    if ($selectionQty) {
                        $price = $product->getPriceModel()->getSelectionPrice($product, $selection, $selectionQty->getValue());

                        $option = $options->getItemById($selection->getOptionId());
                        if (!isset($bundleOptions[$option->getId()])) {
                            $bundleOptions[$option->getId()] = array(
                                'option_id' => $option->getId(),
                                'label' => $option->getTitle(),
                                'value' => array()
                            );
                        }

                        $bundleOptions[$option->getId()]['value'][] = array(
                            'title' => $selection->getName(),
                            'qty'   => $selectionQty->getValue(),
                            'price' => Mage::app()->getStore()->convertPrice($price)
                        );

                    }
                }
            }
        }

        $optionArr['bundle_options'] = $bundleOptions;

        /**
         * Product Prices calculations save
         */
        if ($product->getPriceType()) {
            $optionArr['product_calculations'] = self::CALCULATE_PARENT;
        } else {
            $optionArr['product_calculations'] = self::CALCULATE_CHILD;
        }

        $optionArr['shipment_type'] = $product->getShipmentType();

        return $optionArr;
    }

    public function shakeSelections($a, $b)
    {
        $aPosition = ($a->getOption()->getPosition()+1)*($a->getPosition()+1);
        $bPosition = ($b->getOption()->getPosition()+1)*($b->getPosition()+1);
        if ($aPosition == $bPosition) {
            if ($a->getSelectionId() == $b->getSelectionId()) {
                return 0;
            }
            return ($a->getSelectionId() < $b->getSelectionId()) ? -1 : 1;
        }
        return ($aPosition < $bPosition) ? -1 : 1;
    }

    /**
     * Return true if product has options
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function hasOptions($product = null)
    {
        $product    = $this->getProduct($product);
        $this->setStoreFilter($product->getStoreId(), $product);
        $optionIds  = $this->getOptionsCollection($product)->getAllIds();
        $collection = $this->getSelectionsCollection($optionIds, $product);

        if (count($collection) > 0 || $product->getOptions()) {
            return true;
        }

        return false;
    }

    /**
     * Allow for updates of chidren qty's
     *
     * @param Mage_Catalog_Model_Product $product
     * @return boolean true
     */
    public function getForceChildItemQtyChanges($product = null)
    {
        return true;
    }

    /**
     * Retrieve additional searchable data from type instance
     * Using based on product id and store_id data
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getSearchableData($product = null)
    {
        $searchData = parent::getSearchableData($product);
        $product = $this->getProduct($product);

        $optionSearchData = Mage::getSingleton('bundle/option')
            ->getSearchableData($product->getId(), $product->getStoreId());
        if ($optionSearchData) {
            $searchData = array_merge($searchData, $optionSearchData);
        }

        return $searchData;
    }

    /**
     * Check if product can be bought
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Bundle_Model_Product_Type
     * @throws Mage_Core_Exception
     */
    public function checkProductBuyState($product = null)
    {
        parent::checkProductBuyState($product);
        $product = $this->getProduct($product);
        $productOptionIds = $this->getOptionsIds($product);
        $productSelections = $this->getSelectionsCollection($productOptionIds, $product);

        $selectionIds   = $product->getCustomOption('bundle_selection_ids');
        $selectionIds   = unserialize($selectionIds->getValue());
        foreach ($selectionIds as $selectionId) {
            /* @var $selection Mage_Bundle_Model_Selection */
            $selection = $productSelections->getItemById($selectionId);
            if (!$selection || !$selection->isSalable()) {
                Mage::throwException(
                    Mage::helper('bundle')->__('Selected required options are not available.')
                );
            }
        }

        return $this;
    }

    /**
     * Retrieve products divided into groups required to purchase
     * At least one product in each group has to be purchased
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getProductsToPurchaseByReqGroups($product = null)
    {
        $product = $this->getProduct($product);
        $groups = array();
        $allProducts = array();
        $hasRequiredOptions = false;
        foreach ($this->getOptions($product) as $option) {
            $groupProducts = array();
            foreach ($this->getSelectionsCollection(array($option->getId()), $product) as $childProduct) {
                $groupProducts[] = $childProduct;
                $allProducts[] = $childProduct;
            }
            if ($option->getRequired()) {
                $groups[] = $groupProducts;
                $hasRequiredOptions = true;
            }
        }
        if (!$hasRequiredOptions) {
            $groups = array($allProducts);
        }
        return $groups;
    }
}
