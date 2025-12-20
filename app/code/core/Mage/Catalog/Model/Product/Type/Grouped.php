<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Grouped product type implementation
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Type_Grouped extends Mage_Catalog_Model_Product_Type_Abstract
{
    public const TYPE_CODE = 'grouped';

    /**
     * Cache key for Associated Products
     *
     * @var string
     */
    protected $_keyAssociatedProducts = '_cache_instance_associated_products';

    /**
     * Cache key for Associated Product Ids
     *
     * @var string
     */
    protected $_keyAssociatedProductIds = '_cache_instance_associated_product_ids';

    /**
     * Cache key for Status Filters
     *
     * @var string
     */
    protected $_keyStatusFilters = '_cache_instance_status_filters';

    /**
     * Product is composite properties
     *
     * @var bool
     */
    protected $_isComposite = true;

    /**
     * Product is configurable
     *
     * @var bool
     */
    protected $_canConfigure = true;

    /**
     * Attributes used in associated products
     *
     * @var string|string[]
     */
    protected $_attributesUsedInAssociatedProducts = '*';

    /**
     * @return string|string[]
     */
    public function getAttributesUsedInAssociatedProducts()
    {
        return $this->_attributesUsedInAssociatedProducts;
    }

    /**
     * @param  string|string[] $attribute
     * @return $this
     */
    public function setAttributesUsedInAssociatedProducts($attribute)
    {
        $this->_attributesUsedInAssociatedProducts = $attribute;
        return $this;
    }

    /**
     * Return relation info about used products
     *
     * @return Varien_Object Object with information data
     */
    public function getRelationInfo()
    {
        $info = new Varien_Object();
        $info->setTable('catalog/product_link')
            ->setParentFieldName('product_id')
            ->setChildFieldName('linked_product_id')
            ->setWhere('link_type_id=' . Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);
        return $info;
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param  int   $parentId
     * @param  bool  $required
     * @return array
     */
    public function getChildrenIds($parentId, $required = true)
    {
        return Mage::getResourceSingleton('catalog/product_link')
            ->getChildrenIds(
                $parentId,
                Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED,
            );
    }

    /**
     * Retrieve parent ids array by requered child
     *
     * @param  array|int $childId
     * @return array
     */
    public function getParentIdsByChild($childId)
    {
        return Mage::getResourceSingleton('catalog/product_link')
            ->getParentIdsByChild(
                $childId,
                Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED,
            );
    }

    /**
     * Retrieve array of associated products
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getAssociatedProducts($product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_keyAssociatedProducts)) {
            $associatedProducts = [];

            if (!Mage::app()->getStore()->isAdmin()) {
                $this->setSaleableStatus($product);
            }

            $collection = $this->getAssociatedProductCollection($product)
                ->addAttributeToSelect($this->getAttributesUsedInAssociatedProducts())
                ->addFilterByRequiredOptions()
                ->setPositionOrder()
                ->addStoreFilter($this->getStoreFilter($product))
                ->addAttributeToFilter('status', ['in' => $this->getStatusFilters($product)]);

            foreach ($collection as $item) {
                $associatedProducts[] = $item;
            }

            $this->getProduct($product)->setData($this->_keyAssociatedProducts, $associatedProducts);
        }

        return $this->getProduct($product)->getData($this->_keyAssociatedProducts);
    }

    /**
     * Add status filter to collection
     *
     * @param  int                        $status
     * @param  Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function addStatusFilter($status, $product = null)
    {
        $statusFilters = $this->getProduct($product)->getData($this->_keyStatusFilters);
        if (!is_array($statusFilters)) {
            $statusFilters = [];
        }

        $statusFilters[] = $status;
        $this->getProduct($product)->setData($this->_keyStatusFilters, $statusFilters);

        return $this;
    }

    /**
     * Set only saleable filter
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function setSaleableStatus($product = null)
    {
        $this->getProduct($product)->setData(
            $this->_keyStatusFilters,
            Mage::getSingleton('catalog/product_status')->getSaleableStatusIds(),
        );
        return $this;
    }

    /**
     * Return all assigned status filters
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getStatusFilters($product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_keyStatusFilters)) {
            return [
                Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
                Mage_Catalog_Model_Product_Status::STATUS_DISABLED,
            ];
        }

        return $this->getProduct($product)->getData($this->_keyStatusFilters);
    }

    /**
     * Retrieve related products identifiers
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getAssociatedProductIds($product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_keyAssociatedProductIds)) {
            $associatedProductIds = [];
            foreach ($this->getAssociatedProducts($product) as $item) {
                $associatedProductIds[] = $item->getId();
            }

            $this->getProduct($product)->setData($this->_keyAssociatedProductIds, $associatedProductIds);
        }

        return $this->getProduct($product)->getData($this->_keyAssociatedProductIds);
    }

    /**
     * Retrieve collection of associated products
     *
     * @param  Mage_Catalog_Model_Product                                  $product
     * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    public function getAssociatedProductCollection($product = null)
    {
        $collection = $this->getProduct($product)->getLinkInstance()->useGroupedLinks()
            ->getProductCollection()
            ->setFlag('require_stock_items', true)
            ->setFlag('product_children', true)
            ->setIsStrongMode();
        $collection->setProduct($this->getProduct($product));
        return $collection;
    }

    /**
     * Check is product available for sale
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isSalable($product = null)
    {
        $salable = parent::isSalable($product);
        if (!is_null($salable)) {
            return $salable;
        }

        $salable = false;
        foreach ($this->getAssociatedProducts($product) as $associatedProduct) {
            $salable = $salable || $associatedProduct->isSalable();
        }

        return $salable;
    }

    /**
     * Save type related data
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function save($product = null)
    {
        parent::save($product);
        $this->getProduct($product)->getLinkInstance()->saveGroupedLinks($this->getProduct($product));
        return $this;
    }

    /**
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and add logic specific to Grouped product type.
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  string                     $processMode
     * @return array|string
     */
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        $product = $this->getProduct($product);
        $productsInfo = $buyRequest->getSuperGroup();
        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);

        if (!$isStrictProcessMode || (!empty($productsInfo) && is_array($productsInfo))) {
            $products = [];
            $associatedProductsInfo = [];
            $associatedProducts = $this->getAssociatedProducts($product);
            if ($associatedProducts || !$isStrictProcessMode) {
                foreach ($associatedProducts as $subProduct) {
                    $subProductId = $subProduct->getId();
                    if (isset($productsInfo[$subProductId])) {
                        $qty = $productsInfo[$subProductId];
                        if (!empty($qty) && is_numeric($qty)) {
                            $_result = $subProduct->getTypeInstance(true)
                                ->_prepareProduct($buyRequest, $subProduct, $processMode);
                            if (is_string($_result)) {
                                return $_result;
                            }

                            if (!isset($_result[0])) {
                                return Mage::helper('checkout')->__('Cannot process the item.');
                            }

                            if ($isStrictProcessMode) {
                                $_result[0]->setCartQty($qty);
                                $_result[0]->addCustomOption('product_type', self::TYPE_CODE, $product);
                                $_result[0]->addCustomOption(
                                    'info_buyRequest',
                                    serialize([
                                        'super_product_config' => [
                                            'product_type'  => self::TYPE_CODE,
                                            'product_id'    => $product->getId(),
                                        ],
                                    ]),
                                );
                                $products[] = $_result[0];
                            } else {
                                $associatedProductsInfo[] = [$subProductId => $qty];
                                $product->addCustomOption('associated_product_' . $subProductId, $qty);
                            }
                        }
                    }
                }
            }

            if (!$isStrictProcessMode || count($associatedProductsInfo)) {
                $product->addCustomOption('product_type', self::TYPE_CODE, $product);
                $product->addCustomOption('info_buyRequest', serialize($buyRequest->getData()));

                $products[] = $product;
            }

            if ($products !== []) {
                return $products;
            }
        }

        return Mage::helper('catalog')->__('Please specify the quantity of product(s).');
    }

    /**
     * Retrieve products divided into groups required to purchase
     * At least one product in each group has to be purchased
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getProductsToPurchaseByReqGroups($product = null)
    {
        $product = $this->getProduct($product);
        return [$this->getAssociatedProducts($product)];
    }

    /**
     * Prepare selected qty for grouped product's options
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  Varien_Object              $buyRequest
     * @return array
     */
    public function processBuyRequest($product, $buyRequest)
    {
        $superGroup = $buyRequest->getSuperGroup();
        $superGroup = (is_array($superGroup)) ? array_filter($superGroup, \intval(...)) : [];

        return ['super_group' => $superGroup];
    }
}
