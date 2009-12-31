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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grouped product type implementation
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Type_Grouped extends Mage_Catalog_Model_Product_Type_Abstract
{
    const TYPE_CODE = 'grouped';

    /**
     * Cache key for Associated Products
     *
     * @var string
     */
    protected $_keyAssociatedProducts   = '_cache_instance_associated_products';

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
    protected $_keyStatusFilters        = '_cache_instance_status_filters';

    /**
     * Product is composite properties
     *
     * @var bool
     */
    protected $_isComposite             = true;

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
     * @param int $parentId
     * @param bool $required
     * @return array
     */
    public function getChildrenIds($parentId, $required = true)
    {
        return Mage::getResourceSingleton('catalog/product_link')
            ->getChildrenIds($parentId,
                Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);
    }

    /**
     * Retrieve parent ids array by requered child
     *
     * @param int|array $childId
     * @return array
     */
    public function getParentIdsByChild($childId)
    {
        return Mage::getResourceSingleton('catalog/product_link')
            ->getParentIdsByChild($childId,
                Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);
    }

    /**
     * Retrieve array of associated products
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getAssociatedProducts($product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_keyAssociatedProducts)) {
            $associatedProducts = array();

            if (!Mage::app()->getStore()->isAdmin()) {
                $this->setSaleableStatus($product);
            }

            $collection = $this->getAssociatedProductCollection($product)
                ->addAttributeToSelect('*')
                ->addFilterByRequiredOptions()
                ->setPositionOrder()
                ->addStoreFilter($this->getStoreFilter($product))
                ->addAttributeToFilter('status', array('in' => $this->getStatusFilters($product)));

            foreach ($collection as $product) {
                $associatedProducts[] = $product;
            }

            $this->getProduct($product)->setData($this->_keyAssociatedProducts, $associatedProducts);
        }
        return $this->getProduct($product)->getData($this->_keyAssociatedProducts);
    }

    /**
     * Add status filter to collection
     *
     * @param  int $status
     * @param  Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product_Type_Grouped
     */
    public function addStatusFilter($status, $product = null)
    {
        $statusFilters = $this->getProduct($product)->getData($this->_keyStatusFilters);
        if (!is_array($statusFilters)) {
            $statusFilters = array();
        }

        $statusFilters[] = $status;
        $this->getProduct($product)->setData($this->_keyStatusFilters, $statusFilters);

        return $this;
    }

    /**
     * Set only saleable filter
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product_Type_Grouped
     */
    public function setSaleableStatus($product = null)
    {
        $this->getProduct($product)->setData($this->_keyStatusFilters,
            Mage::getSingleton('catalog/product_status')->getSaleableStatusIds());
        return $this;
    }

    /**
     * Return all assigned status filters
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getStatusFilters($product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_keyStatusFilters)) {
            return array(
                Mage_Catalog_Model_Product_Status::STATUS_ENABLED,
                Mage_Catalog_Model_Product_Status::STATUS_DISABLED
            );
        }
        return $this->getProduct($product)->getData($this->_keyStatusFilters);
    }

    /**
     * Retrieve related products identifiers
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getAssociatedProductIds($product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_keyAssociatedProductIds)) {
            $associatedProductIds = array();
            foreach ($this->getAssociatedProducts($product) as $product) {
                $associatedProductIds[] = $product->getId();
            }
            $this->getProduct($product)->setData($this->_keyAssociatedProductIds, $associatedProductIds);
        }
        return $this->getProduct($product)->getData($this->_keyAssociatedProductIds);
    }

    /**
     * Retrieve collection of associated products
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Product_Collection
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
     * @param Mage_Catalog_Model_Product $product
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
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product_Type_Grouped
     */
    public function save($product = null)
    {
        parent::save($product);
        $this->getProduct($product)->getLinkInstance()->saveGroupedLinks($this->getProduct($product));
        return $this;
    }

    /**
     * Initialize product(s) for add to cart process
     *
     * @param   Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @return  array || string || false
     */
    public function prepareForCart(Varien_Object $buyRequest, $product = null)
    {
        $product = $this->getProduct($product);
        $productsInfo = $buyRequest->getSuperGroup();
        if (!empty($productsInfo) && is_array($productsInfo)) {
            $products = array();
            $associatedProducts = $this->getAssociatedProducts($product);
            if ($associatedProducts) {
                foreach ($associatedProducts as $subProduct) {
                    if(isset($productsInfo[$subProduct->getId()])) {
                        $qty = $productsInfo[$subProduct->getId()];
                        if (!empty($qty)) {

                            $_result = $subProduct->getTypeInstance(true)
                                ->prepareForCart($buyRequest, $subProduct);
                            if (is_string($_result) && !is_array($_result)) {
                                return $_result;
                            }

                            if (!isset($_result[0])) {
                                return Mage::helper('checkout')->__('Can not add item to shopping cart');
                            }

                            $_result[0]->setCartQty($qty);
                            $_result[0]->addCustomOption('product_type', self::TYPE_CODE, $product);
                            $_result[0]->addCustomOption('info_buyRequest',
                                serialize(array(
                                    'super_product_config' => array(
                                        'product_type'  => self::TYPE_CODE,
                                        'product_id'    => $product->getId()
                                    )
                                ))
                            );
                            $products[] = $_result[0];
                        }
                    }
                }
            }
            if (count($products)) {
                return $products;
            }
        }
        return Mage::helper('catalog')->__('Please specify the product(s) quantity');
    }
}
