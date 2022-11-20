<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog category api
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Category_Api extends Mage_Catalog_Model_Api_Resource
{
    public function __construct()
    {
        $this->_storeIdSessionField = 'category_store_id';
    }

    /**
     * Retrieve level of categories for category/store view/website
     *
     * @param string|int|null $website
     * @param string|int|null $store
     * @param int|null $categoryId
     * @return array
     */
    public function level($website = null, $store = null, $categoryId = null)
    {
        $ids = [];
        $storeId = Mage_Catalog_Model_Category::DEFAULT_STORE_ID;

        // load root categories of website
        if ($website !== null) {
            try {
                $website = Mage::app()->getWebsite($website);
                if ($store === null) {
                    if ($categoryId === null) {
                        foreach ($website->getStores() as $store) {
                            $ids[] = $store->getRootCategoryId();
                        }
                    } else {
                        $ids = $categoryId;
                    }
                } elseif (in_array($store, $website->getStoreIds())) {
                    $storeId = Mage::app()->getStore($store);
                    $ids = $categoryId ?? $store->getRootCategoryId();
                } else {
                    $this->_fault('store_not_exists');
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('website_not_exists', $e->getMessage());
            }
        } elseif ($store !== null) {
            // load children of root category of store
            if ($categoryId === null) {
                try {
                    $store = Mage::app()->getStore($store);
                    $storeId = $store->getId();
                    $ids = $store->getRootCategoryId();
                } catch (Mage_Core_Model_Store_Exception $e) {
                    $this->_fault('store_not_exists');
                }
            } else { // load children of specified category id
                $storeId = $this->_getStoreId($store);
                $ids = (int)$categoryId;
            }
        } else { // load all root categories
            $ids = $categoryId ?? Mage_Catalog_Model_Category::TREE_ROOT_ID;
        }

        $collection = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($storeId)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');

        if (is_array($ids)) {
            $collection->addFieldToFilter('entity_id', ['in' => $ids]);
        } else {
            $collection->addFieldToFilter('parent_id', $ids);
        }

        // Only basic category data
        $result = [];
        foreach ($collection as $category) {
            /** @var Mage_Catalog_Model_Category $category */
            $result[] = [
                'category_id' => $category->getId(),
                'parent_id'   => $category->getParentId(),
                'name'        => $category->getName(),
                'is_active'   => $category->getIsActive(),
                'position'    => $category->getPosition(),
                'level'       => $category->getLevel()
            ];
        }

        return $result;
    }

    /**
     * Retrieve category tree
     *
     * @param null $parentId
     * @param string|int $store
     * @return array
     * @throws Mage_Core_Model_Store_Exception
     */
    public function tree($parentId = null, $store = null)
    {
        if (is_null($parentId) && !is_null($store)) {
            $parentId = Mage::app()->getStore($this->_getStoreId($store))->getRootCategoryId();
        } elseif (is_null($parentId)) {
            $parentId = 1;
        }

        /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree $tree */
        $tree = Mage::getResourceSingleton('catalog/category_tree')
            ->load();

        $root = $tree->getNodeById($parentId);

        if ($root && $root->getId() == 1) {
            $root->setName(Mage::helper('catalog')->__('Root'));
        }

        $collection = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($this->_getStoreId($store))
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');

        $tree->addCollectionData($collection, true);

        return $this->_nodeToArray($root);
    }

    /**
     * Convert node to array
     *
     * @param Varien_Data_Tree_Node $node
     * @return array
     */
    protected function _nodeToArray(Varien_Data_Tree_Node $node)
    {
        // Only basic category data
        $result = [];
        $result['category_id'] = $node->getId();
        $result['parent_id']   = $node->getParentId();
        $result['name']        = $node->getName();
        $result['is_active']   = $node->getIsActive();
        $result['position']    = $node->getPosition();
        $result['level']       = $node->getLevel();
        $result['children']    = [];

        foreach ($node->getChildren() as $child) {
            $result['children'][] = $this->_nodeToArray($child);
        }

        return $result;
    }

    /**
     * Initilize and return category model
     *
     * @param int $categoryId
     * @param string|int $store
     * @return Mage_Catalog_Model_Category
     */
    protected function _initCategory($categoryId, $store = null)
    {
        $category = Mage::getModel('catalog/category')
            ->setStoreId($this->_getStoreId($store))
            ->load($categoryId);

        if (!$category->getId()) {
            $this->_fault('not_exists');
        }

        return $category;
    }

    /**
     * Retrieve category data
     *
     * @param int $categoryId
     * @param string|int $store
     * @param array $attributes
     * @return array
     */
    public function info($categoryId, $store = null, $attributes = null)
    {
        $category = $this->_initCategory($categoryId, $store);

        // Basic category data
        $result = [];
        $result['category_id'] = $category->getId();

        $result['is_active']   = $category->getIsActive();
        $result['position']    = $category->getPosition();
        $result['level']       = $category->getLevel();

        foreach ($category->getAttributes() as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $attributes)) {
                $result[$attribute->getAttributeCode()] = $category->getData($attribute->getAttributeCode());
            }
        }
        $result['parent_id']   = $category->getParentId();
        $result['children']           = $category->getChildren();
        $result['all_children']       = $category->getAllChildren();

        return $result;
    }

    /**
     * Create new category
     *
     * @param int $parentId
     * @param array $categoryData
     * @param int|null|string $store
     * @return int
     */
    public function create($parentId, $categoryData, $store = null)
    {
        $parent_category = $this->_initCategory($parentId, $store);

        /** @var Mage_Catalog_Model_Category $category */
        $category = Mage::getModel('catalog/category')
            ->setStoreId($this->_getStoreId($store));

        $category->addData(['path' => implode('/', $parent_category->getPathIds())]);
        $category->setAttributeSetId($category->getDefaultAttributeSetId());

        $useConfig = [];
        foreach ($category->getAttributes() as $attribute) {
            if ($this->_isAllowedAttribute($attribute)
                && isset($categoryData[$attribute->getAttributeCode()])
            ) {
                // check whether value is 'use_config'
                $attrCode = $attribute->getAttributeCode();
                $categoryDataValue = $categoryData[$attrCode];
                if ($categoryDataValue === 'use_config' ||
                    (is_array($categoryDataValue) &&
                    count($categoryDataValue) == 1 &&
                    $categoryDataValue[0] === 'use_config')
                ) {
                    $useConfig[] = $attrCode;
                    $category->setData($attrCode, null);
                } else {
                    $category->setData($attrCode, $categoryDataValue);
                }
            }
        }

        $category->setParentId($parent_category->getId());

        /**
         * Proceed with $useConfig set into category model for processing through validation
         */
        if (count($useConfig) > 0) {
            $category->setData("use_post_data_config", $useConfig);
        }

        try {
            $validate = $category->validate();
            if ($validate !== true) {
                foreach ($validate as $code => $error) {
                    if ($error === true) {
                        Mage::throwException(Mage::helper('catalog')->__('Attribute "%s" is required.', $code));
                    } else {
                        Mage::throwException($error);
                    }
                }
            }

            $category->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $category->getId();
    }

    /**
     * Update category data
     *
     * @param int $categoryId
     * @param array $categoryData
     * @param string|int $store
     * @return bool
     */
    public function update($categoryId, $categoryData, $store = null)
    {
        $category = $this->_initCategory($categoryId, $store);

        foreach ($category->getAttributes() as $attribute) {
            if ($this->_isAllowedAttribute($attribute)
                && isset($categoryData[$attribute->getAttributeCode()])) {
                $category->setData(
                    $attribute->getAttributeCode(),
                    $categoryData[$attribute->getAttributeCode()]
                );
            }
        }

        try {
            $validate = $category->validate();
            if ($validate !== true) {
                foreach ($validate as $code => $error) {
                    if ($error === true) {
                        Mage::throwException(Mage::helper('catalog')->__('Attribute "%s" is required.', $code));
                    } else {
                        Mage::throwException($error);
                    }
                }
            }

            $category->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Move category in tree
     *
     * @param int $categoryId
     * @param int $parentId
     * @param int $afterId
     * @return bool
     */
    public function move($categoryId, $parentId, $afterId = null)
    {
        $category = $this->_initCategory($categoryId);
        $parent_category = $this->_initCategory($parentId);

        // if $afterId is null - move category to the down
        if ($afterId === null && $parent_category->hasChildren()) {
            $parentChildren = $parent_category->getChildren();
            $afterId = array_pop(explode(',', $parentChildren));
        }

        if (strpos($parent_category->getPath(), $category->getPath()) === 0) {
            $this->_fault('not_moved', "Operation do not allow to move a parent category to any of children category");
        }

        try {
            $category->move($parentId, $afterId);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_moved', $e->getMessage());
        }

        return true;
    }

    /**
     * Delete category
     *
     * @param int $categoryId
     * @return bool
     */
    public function delete($categoryId)
    {
        if (Mage_Catalog_Model_Category::TREE_ROOT_ID == $categoryId) {
            $this->_fault('not_deleted', 'Cannot remove the system category.');
        }

        $category = $this->_initCategory($categoryId);

        try {
            $category->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
    }

    /**
     * Get the product ID from a product ID or SKU. When $identifierType is left empty the helper will try to
     * automatically parse the given $productId and determine if it is a SKU or ID value
     *
     * @param int|string  $productId      The product ID or SKU
     * @param null|string $identifierType Should be 'sku' when working with SKU's. Leave null when using ID's
     *
     * @return int
     * @throws Mage_Api_Exception
     */
    protected function _getProductId($productId, $identifierType = null)
    {
        $product = Mage::helper('catalog/product')->getProduct($productId, null, $identifierType);
        if (!$product->getId()) {
            $this->_fault('not_exists', 'Product not exists.');
        }
        return $product->getId();
    }

    /**
     * Retrieve list of assigned products to category
     *
     * @param int $categoryId
     * @param string|int $store
     * @return array
     */
    public function assignedProducts($categoryId, $store = null)
    {
        $category = $this->_initCategory($categoryId);

        $storeId = $this->_getStoreId($store);
        $collection = $category->setStoreId($storeId)->getProductCollection();
        ($storeId == 0) ? $collection->addOrder('position', 'asc') : $collection->setOrder('position', 'asc');

        $result = [];

        foreach ($collection as $product) {
            $result[] = [
                'product_id' => $product->getId(),
                'type'       => $product->getTypeId(),
                'set'        => $product->getAttributeSetId(),
                'sku'        => $product->getSku(),
                'position'   => $product->getCatIndexPosition()
            ];
        }

        return $result;
    }

    /**
     * Assign product to category
     *
     * @param int $categoryId
     * @param int $productId
     * @param int $position
     * @param null|string $identifierType Should be 'sku' when working with SKU's. Leave null when using ID's
     * @return bool
     * @throws Mage_Api_Exception
     */
    public function assignProduct($categoryId, $productId, $position = null, $identifierType = null)
    {
        $category = $this->_initCategory($categoryId);
        $positions = $category->getProductsPosition();
        $productId = $this->_getProductId($productId, $identifierType);
        $positions[$productId] = $position;
        $category->setPostedProducts($positions);

        try {
            $category->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Update product assignment
     *
     * @param int $categoryId
     * @param int $productId
     * @param int $position
     * @param null $identifierType
     * @return bool
     * @throws Mage_Api_Exception
     */
    public function updateProduct($categoryId, $productId, $position = null, $identifierType = null)
    {
        $category = $this->_initCategory($categoryId);
        $positions = $category->getProductsPosition();
        $productId = $this->_getProductId($productId, $identifierType);
        if (!isset($positions[$productId])) {
            $this->_fault('product_not_assigned');
        }
        $positions[$productId] = $position;
        $category->setPostedProducts($positions);

        try {
            $category->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Remove product assignment from category
     *
     * @param int $categoryId
     * @param int $productId
     * @param null $identifierType
     * @return bool
     * @throws Mage_Api_Exception
     */
    public function removeProduct($categoryId, $productId, $identifierType = null)
    {
        $category = $this->_initCategory($categoryId);
        $positions = $category->getProductsPosition();
        $productId = $this->_getProductId($productId, $identifierType);
        if (!isset($positions[$productId])) {
            $this->_fault('product_not_assigned');
        }

        unset($positions[$productId]);
        $category->setPostedProducts($positions);

        try {
            $category->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }
}
