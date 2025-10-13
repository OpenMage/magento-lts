<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * SEO tree Categories Sitemap block
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Seo_Sitemap_Tree_Category extends Mage_Catalog_Block_Seo_Sitemap_Category
{
    public const XML_PATH_LINES_PER_PAGE = 'catalog/sitemap/lines_perpage';

    protected $_storeRootCategoryPath = '';

    protected $_storeRootCategoryLevel = 0;

    protected $_total = 0;

    protected $_from = 0;

    protected $_to = 0;

    protected $_currentPage = 0;

    protected $_categoriesToPages = [];

    /**
     * Initialize categories collection
     *
     * @return Mage_Catalog_Block_Seo_Sitemap_Category
     */
    protected function _prepareLayout()
    {
        $parent = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load(Mage::app()->getStore()->getRootCategoryId());
        $this->_storeRootCategoryPath = $parent->getPath();
        $this->_storeRootCategoryLevel = $parent->getLevel();
        $this->prepareCategoriesToPages();
        $collection = $this->getTreeCollection();
        $this->setCollection($collection);
        return $this;
    }

    /**
     * Init pager
     *
     * @param string $pagerName
     */
    public function bindPager($pagerName)
    {
        $pager = $this->getLayout()->getBlock($pagerName);
        /** @var Mage_Catalog_Block_Seo_Sitemap_Tree_Pager $pager */
        if ($pager) {
            $pager->setAvailableLimit([50 => 50]);
            $pager->setTotalNum($this->_total);
            $pager->setLastPageNum(count($this->_categoriesToPages));
            if (!$this->_currentPage) {
                $this->_currentPage = $pager->getCurrentPage();
                $this->_prepareCollection();
            }

            $pager->setFirstNum($this->_from);
            $pager->setLastNum($this->_to);
            $pager->setCollection($this->getCollection());
            $pager->setShowPerPage(false);
        }
    }

    /**
     * Prepare array of categories separated into pages
     *
     * @return $this
     */
    public function prepareCategoriesToPages()
    {
        $linesPerPage = Mage::getStoreConfig(self::XML_PATH_LINES_PER_PAGE);
        $tmpCollection = Mage::getModel('catalog/category')->getCollection()
            ->addIsActiveFilter()
            ->addPathsFilter($this->_storeRootCategoryPath . '/')
            ->addLevelFilter($this->_storeRootCategoryLevel + 1)
            ->addOrderField('path');
        $count = 0;
        $page = 1;
        $categories = [];
        foreach ($tmpCollection as $item) {
            $children = $item->getChildrenCount() + 1;
            $this->_total += $children;
            if (($children + $count) >= $linesPerPage) {
                $categories[$page][$item->getId()] = [
                    'path' => $item->getPath(),
                    'children_count' => $this->_total,
                ];
                $page++;
                $count = 0;
                continue;
            }

            $categories[$page][$item->getId()] = [
                'path' => $item->getPath(),
                'children_count' => $this->_total,
            ];
            $count += $children;
        }

        $this->_categoriesToPages = $categories;
        return $this;
    }

    /**
     * Return collection of categories
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getTreeCollection()
    {
        return Mage::getModel('catalog/category')->getCollection()
            ->addNameToResult()
            ->addUrlRewriteToResult()
            ->addIsActiveFilter()
            ->addOrderField('path');
    }

    /**
     * Prepare collection filtered by paths
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $_to = 0;
        $pathFilter = [];
        if (isset($this->_categoriesToPages[$this->_currentPage])) {
            foreach ($this->_categoriesToPages[$this->_currentPage] as $_categoryInfo) {
                $pathFilter[] = $_categoryInfo['path'];
                $_to = max($_to, $_categoryInfo['children_count']);
            }
        }

        if (empty($pathFilter)) {
            $pathFilter = $this->_storeRootCategoryPath . '/';
        }

        $collection = $this->getCollection();
        $collection->addPathsFilter($pathFilter);

        $this->_to = $_to;
        $this->_from = $_to - $collection->count();
        return $this;
    }

    /**
     * Return level of indent
     *
     * @param Mage_Catalog_Model_Category $item
     * @param int $delta
     * @return int
     */
    public function getLevel($item, $delta = 1)
    {
        return (int) ($item->getLevel() - $this->_storeRootCategoryLevel - 1) * $delta;
    }
}
