<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Categories tree block for urlrewrites
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Urlrewrite_Category_Tree extends Mage_Adminhtml_Block_Catalog_Category_Abstract
{
    /**
     * List of allowed category ids
     *
     * @var array|null
     */
    protected $_allowedCategoryIds = null;

    /**
     * Set custom template for the block
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('urlrewrite/categories.phtml');
    }

    /**
     * Get categories tree as recursive array
     *
     * @param int $parentId
     * @param bool $asJson
     * @param int $recursionLevel
     * @return array|string
     */
    public function getTreeArray($parentId = null, $asJson = false, $recursionLevel = 3)
    {
        $productId = Mage::app()->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')->setId($productId);
            $this->_allowedCategoryIds = $product->getCategoryIds();
            unset($product);
        }

        $result = [];
        if ($parentId) {
            $category = Mage::getModel('catalog/category')->load($parentId);
            if (!empty($category)) {
                $tree = $this->_getNodesArray($this->getNode($category, $recursionLevel));
                if (!empty($tree) && !empty($tree['children'])) {
                    $result = $tree['children'];
                }
            }
        } else {
            $result = $this->_getNodesArray($this->getRoot(null, $recursionLevel));
        }

        if ($asJson) {
            return Mage::helper('core')->jsonEncode($result);
        }

        $this->_allowedCategoryIds = null;

        return $result;
    }

    /**
     * Get categories collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     */
    public function getCategoryCollection()
    {
        $collection = $this->_getData('category_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect(['name', 'is_active'])
                ->setLoadProductCount(true);
            $this->setData('category_collection', $collection);
        }

        return $collection;
    }

    /**
     * Convert categories tree to array recursively
     *
     * @param  Varien_Data_Tree_Node $node
     * @return array
     */
    protected function _getNodesArray($node)
    {
        $result = [
            'id'             => (int) $node->getId(),
            'parent_id'      => (int) $node->getParentId(),
            'children_count' => (int) $node->getChildrenCount(),
            'is_active'      => (bool) $node->getIsActive(),
            'name'           => $this->escapeHtml($node->getName()),
            'level'          => (int) $node->getLevel(),
            'product_count'  => (int) $node->getProductCount(),
        ];

        if (is_array($this->_allowedCategoryIds) && !in_array($result['id'], $this->_allowedCategoryIds)) {
            $result['disabled'] = true;
        }

        if ($node->hasChildren()) {
            $result['children'] = [];
            foreach ($node->getChildren() as $childNode) {
                $result['children'][] = $this->_getNodesArray($childNode);
            }
        }
        $result['cls']      = ($result['is_active'] ? '' : 'no-') . 'active-category';
        $result['expanded'] = (!empty($result['children']));

        return $result;
    }

    /**
     * Get URL for categories tree ajax loader
     *
     * @return string
     */
    public function getLoadTreeUrl()
    {
        return Mage::helper('adminhtml')->getUrl('*/*/categoriesJson');
    }
}
