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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog category
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Category extends Mage_Catalog_Model_Abstract
{
    /**
     * Category display modes
     */
    const DM_PRODUCT        = 'PRODUCTS';
    const DM_PAGE           = 'PAGE';
    const DM_MIXED          = 'PRODUCTS_AND_PAGE';

    const CACHE_TAG         = 'catalog_category';
    protected $_cacheTag    = 'catalog_category';

    protected $_eventPrefix = 'catalog_category';
    protected $_eventObject = 'category';

    protected static $_url;
    protected static $_urlRewrite;

    private $_designAttributes = array(
        'custom_design',
        'custom_design_apply',
        'custom_design_from',
        'custom_design_to',
        'page_layout',
        'custom_layout_update'
    );

    /**
     * Enter description here...
     *
     * @var Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree
     */
    protected $_treeModel = null;

    protected function _construct()
    {
        $this->_init('catalog/category');
    }

    public function getUrlInstance()
    {
        if (!self::$_url) {
            self::$_url = Mage::getModel('core/url');
        }
        return self::$_url;
    }

    /**
    * @return Mage_Core_Model_Url_Rewrite
    */
    public function getUrlRewrite()
    {
        if (!self::$_urlRewrite) {
            self::$_urlRewrite = Mage::getModel('core/url_rewrite');
        }
        return self::$_urlRewrite;
    }

    /**
     * Retrieve category tree model
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree
     */
    public function getTreeModel()
    {
        return Mage::getResourceModel('catalog/category_tree');
    }

    /**
     * Enter description here...
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree
     */
    public function getTreeModelInstance()
    {
        if (is_null($this->_treeModel)) {
            $this->_treeModel = Mage::getResourceSingleton('catalog/category_tree');
        }
        return $this->_treeModel;
    }

    /**
     * Move category
     *
     * @return Mage_Catalog_Model_Category
     */
    /*
    public function move($parentId)
    {
        $this->getResource()->move($this, $parentId);
        return $this;
    }
    */

    /**
     * Retrieve default attribute set id
     *
     * @return int
     */
    public function getDefaultAttributeSetId()
    {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }

    /**
     * Get category products collection
     *
     * @return Varien_Data_Collection_Db
     */
    public function getProductCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addCategoryFilter($this);
        return $collection;
    }

    /**
     * Retrieve all customer attributes
     *
     * @return array
     */
    public function getAttributes($noDesignAttributes = false)
    {
        $result = $this->getResource()
            ->loadAllAttributes($this)
            ->getSortedAttributes();

        if ($noDesignAttributes){
            foreach ($result as $k=>$a){
                if (in_array($k, $this->_designAttributes)) {
                    unset($result[$k]);
                }
            }
        }

        return $result;
    }

    /**
     * Retrieve array of product id's for category
     *
     * array($productId => $position)
     *
     * @return array
     */
    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return array();
        }

        $arr = $this->getData('products_position');
        if (is_null($arr)) {
            $arr = $this->getResource()->getProductsPosition($this);
            $this->setData('products_position', $arr);
        }
        return $arr;
    }

    /**
     * Retrieve array of store ids for category
     *
     * @return array
     */
    public function getStoreIds()
    {
        if ($this->getInitialSetupFlag()) {
            return array();
        }

        if ($storeIds = $this->getData('store_ids')) {
            return $storeIds;
        }
        $storeIds = $this->getResource()->getStoreIds($this);
        $this->setData('store_ids', $storeIds);
        return $storeIds;
    }


    public function getLayoutUpdateHandle()
    {
        $layout = 'catalog_category_';
        if ($this->getIsAnchor()) {
            $layout.= 'layered';
        }
        else {
            $layout.= 'default';
        }
        return $layout;
    }

    public function getStoreId()
    {
        return $this->_getData('store_id');
    }

    /**
     * Get category url
     *
     * @return string
     */
    public function getUrl()
    {
        $url = $this->_getData('url');
        if (is_null($url)) {
            $queryParams = '';
//            $store = Mage::app()->getStore();
//            if ($store->getId() && Mage::getStoreConfig(Mage_Core_Model_Url::XML_PATH_STORE_IN_URL)) {
//                $queryParams = '?store='.$store->getCode();
//            }

	        if ($this->hasData('request_path') && $this->getRequestPath() != '') {
	            $url = $this->getUrlInstance()->getBaseUrl().$this->getRequestPath().$queryParams;
	            $this->setUrl($url);
	            return $url;
	        }

	        Varien_Profiler::start('REWRITE: '.__METHOD__);
	        $rewrite = $this->getUrlRewrite();
	        if ($this->getStoreId()) {
	            $rewrite->setStoreId($this->getStoreId());
	        }
	        $idPath = 'category/'.$this->getId();

	        $rewrite->loadByIdPath($idPath);

	        if ($rewrite->getId()) {
	            $url = $this->getUrlInstance()->getBaseUrl().$rewrite->getRequestPath().$queryParams;
	        Varien_Profiler::stop('REWRITE: '.__METHOD__);
	            $this->setUrl($url);
	            return $url;
	        }
	        Varien_Profiler::stop('REWRITE: '.__METHOD__);

	        $url = $this->getCategoryIdUrl().$queryParams;
	        $this->setUrl($url);
	        return $url;
        }
        return $url;
    }

    public function getCategoryIdUrl()
    {
        Varien_Profiler::start('REGULAR: '.__METHOD__);
        $urlKey = $this->getUrlKey() ? $this->getUrlKey() : $this->formatUrlKey($this->getName());
        $url = $this->getUrlInstance()->getUrl('catalog/category/view', array(
            's'=>$urlKey,
            'id'=>$this->getId(),
        ));
        Varien_Profiler::stop('REGULAR: '.__METHOD__);
        return $url;
    }

    public function formatUrlKey($str)
    {
        $str = Mage::helper('core')->removeAccents($str);
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $str);
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        return $urlKey;
    }

    public function getImageUrl()
    {
        $url = false;
        if ($image = $this->getImage()) {
            $url = Mage::getBaseUrl('media').'catalog/category/'.$image;
        }
        return $url;
    }

    public function getUrlPath()
    {
        if ($path = $this->getData('url_path')) {
            return $path;
        }

        $path = $this->getUrlKey();

        if ($this->getParentId()) {
            $parentPath = Mage::getModel('catalog/category')->load($this->getParentId())->getCategoryPath();
            $path = $parentPath.'/'.$path;
        }

        $this->setUrlPath($path);

        return $path;
    }

    public function getParentCategory()
    {
        return Mage::getModel('catalog/category')->load($this->getParentId());
    }

    public function getParentId()
    {
        $parentPath = explode('/', $this->getPath());
        array_pop($parentPath);
        return intval(array_pop($parentPath));
    }

    public function getCustomDesignDate()
    {
        $result = array();
        $result['from'] = $this->getData('custom_design_from');
        $result['to'] = $this->getData('custom_design_to');

        return $result;
    }

    public function getDesignAttributes()
    {
        $result = array();
        foreach ($this->_designAttributes as $attrName) {
            $result[] = $this->_getAttribute($attrName);
        }
        return $result;
    }

    private function _getAttribute($attributeCode)
    {
        return $this->getResource()
            ->getAttribute($attributeCode);
    }

    public function getAllChildren()
    {
        $children = $this->getTreeModelInstance()->getChildren($this->getId());
        $myId = array($this->getId());
        if (is_array($children)) {
            $children = array_merge($myId, $children);
        } else {
            $children = $myId;
        }
        return implode(',', $children);
    }

    public function getChildren()
    {
        return implode(',', $this->getTreeModelInstance()->getChildren($this->getId(), false));
    }

    public function getPathInStore()
    {
        $result = array();
        //$path = $this->getTreeModelInstance()->getPath($this->getId());
        $path = array_reverse($this->getPathIds());
        foreach ($path as $itemId) {
            if ($itemId == Mage::app()->getStore()->getRootCategoryId())
                break;
            $result[] = $itemId;
        }
        return implode(',', $result);
    }

    /**
     * Check category id exising
     *
     * @param   int $id
     * @return  bool
     */
    public function checkId($id)
    {
        return $this->_getResource()->checkId($id);
    }

    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    public function getLevel()
    {
        if (!$this->hasLevel()) {
            return count(explode('/', $this->getPath())) - 1;
        }
        return $this->getData('level');
    }

    public function verifyIds(array $ids)
    {
        return $this->getResource()->verifyIds($ids);
    }

    public function hasChildren()
    {
        return $this->_getResource()->getChildrenAmount($this) > 0;
    }

    public function getRequestPath()
    {
        return $this->_getData('request_path');
    }

    public function getName()
    {
        return $this->_getData('name');
    }

    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }
}