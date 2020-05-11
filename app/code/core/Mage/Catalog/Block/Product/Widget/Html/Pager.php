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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * New products widget pager block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_Widget_Html_Pager extends Mage_Page_Block_Html_Pager
{
    /**
     * Collection size
     *
     * Size of collection which may has a manual limitation
     *
     * @var int
     */
    protected $_collectionSize;

    /**
     * Current page
     *
     * @var int
     */
    protected $_currentPage;

    /**
     * Last page
     *
     * @var int
     */
    protected $_lastPage;

    /**
     * Return collection size
     *
     * It may be limited by manual
     *
     * @return int
     */
    public function getCollectionSize()
    {
        if (null === $this->_collectionSize) {
            $this->_collectionSize = $this->getCollection()->getSize();
            if ($this->getTotalLimit() && $this->_collectionSize > $this->getTotalLimit()) {
                $this->_collectionSize = $this->getTotalLimit();
            }
        }
        return $this->_collectionSize;
    }

    /**
     * Return number of current page
     *
     * If current page is grate then total count of page current page will be equals total count of page
     *
     * @return int
     */
    public function getCurrentPage()
    {
        if (null === $this->_currentPage) {
            $page = abs((int)$this->getRequest()->getParam($this->getPageVarName()));
            if ($page > $this->getLastPageNum()) {
                $this->_currentPage = $this->getLastPageNum();
            } elseif ($page > 0) {
                $this->_currentPage = $page;
            } else {
                $this->_currentPage = 1;
            }
        }
        return $this->_currentPage;
    }

    /**
     * Return items count per page
     *
     * @return int
     */
    public function getLimit()
    {
        if ($this->_limit > 0) {
            return $this->_limit;
        }
        $limit  = $this->getRequest()->getParam($this->getLimitVarName());
        $limits = $this->getAvailableLimit();
        if ($limit && isset($limits[$limit])) {
            return $limit;
        }
        $limits = array_keys($limits);
        return current($limits);
    }

    /**
     * Connect collection to paging
     *
     * @param   Mage_Core_Model_Resource_Db_Collection_Abstract $collection
     * @return  Mage_Catalog_Block_Product_Widget_Html_Pager
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        $this->_collection->setPageSize(null)->setCurPage(null);

        $collectionOffset = $this->getFirstNum() - 1;
        $collectionLimit  = $collectionOffset + $this->getLimit() > $this->getTotalNum()
            ? $this->getTotalNum() - $collectionOffset
            : $this->getLimit();

        $this->_collection->getSelect()->limit($collectionLimit, $collectionOffset);
        $this->_setFrameInitialized(false);
        return $this;
    }

    /**
     * Return position number in collection for first item on current page
     *
     * @return int
     */
    public function getFirstNum()
    {
        return $this->getLimit() * ($this->getCurrentPage() - 1) + 1;
    }

    /**
     * Return position number in collection for last item on current page
     *
     * @return int
     */
    public function getLastNum()
    {
        $collection = $this->getCollection();
        return $this->getLimit() * ($this->getCurrentPage() - 1) + $collection->count();
    }

    /**
     * Return total number of collection
     *
     * It may be limited by manual
     *
     * @return int
     */
    public function getTotalNum()
    {
        return $this->getCollectionSize();
    }

    /**
     * Return number of last page
     *
     * @return bool
     */
    public function getLastPageNum()
    {
        if (null === $this->_lastPage) {
            $this->_lastPage = ceil($this->getCollectionSize() / $this->getLimit());
            if ($this->_lastPage <= 0) {
                $this->_lastPage = 1;
            }
        }
        return $this->_lastPage;
    }

    /**
     * Checks if current page is the first page
     *
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->getCurrentPage() == 1;
    }

    /**
     * Checks if current page is the last page
     *
     * @return bool
     */
    public function isLastPage()
    {
        return $this->getCurrentPage() >= $this->getLastPageNum();
    }

    /**
     * Return array of pages
     *
     * @return array
     */
    public function getPages()
    {
        $pages = array();
        if ($this->getLastPageNum() <= $this->_displayPages) {
            $pages = range(1, $this->getLastPageNum());
        } else {
            $half = ceil($this->_displayPages / 2);
            if ($this->getCurrentPage() >= $half && $this->getCurrentPage() <= $this->getLastPageNum() - $half) {
                $start  = ($this->getCurrentPage() - $half) + 1;
                $finish = ($start + $this->_displayPages) - 1;
            } elseif ($this->getCurrentPage() < $half) {
                $start  = 1;
                $finish = $this->_displayPages;
            } elseif ($this->getCurrentPage() > ($this->getLastPageNum() - $half)) {
                $finish = $this->getLastPageNum();
                $start  = $finish - $this->_displayPages + 1;
            }
            $pages = range($start, $finish);
        }
        return $pages;
    }

    /**
     * Retrieve url for previous page
     *
     * @return string
     */
    public function getPreviousPageUrl()
    {
        return $this->getPageUrl($this->getCurrentPage() - 1);
    }

    /**
     * Retrieve url for next page
     *
     * @return string
     */
    public function getNextPageUrl()
    {
        return $this->getPageUrl($this->getCurrentPage() + 1);
    }

    /**
     * Retrieve url for last page
     *
     * @return string
     */
    public function getLastPageUrl()
    {
        return $this->getPageUrl($this->getLastPageNum());
    }

    /**
     * Initialize frame data, such as frame start, frame start etc.
     *
     * @return $this
     */
    protected function _initFrame()
    {
        if (!$this->isFrameInitialized()) {
            $start = 0;
            $end   = 0;

            if ($this->getLastPageNum() <= $this->getFrameLength()) {
                $start = 1;
                $end = $this->getLastPageNum();
            } else {
                $half = ceil($this->getFrameLength() / 2);
                if ($this->getCurrentPage() >= $half && $this->getCurrentPage() <= $this->getLastPageNum() - $half) {
                    $start  = ($this->getCurrentPage() - $half) + 1;
                    $end = ($start + $this->getFrameLength()) - 1;
                } elseif ($this->getCurrentPage() < $half) {
                    $start  = 1;
                    $end = $this->getFrameLength();
                } elseif ($this->getCurrentPage() > ($this->getLastPageNum() - $half)) {
                    $end = $this->getLastPageNum();
                    $start  = $end - $this->getFrameLength() + 1;
                }
            }
            $this->_frameStart = $start;
            $this->_frameEnd = $end;
            $this->_setFrameInitialized(true);
        }

        return $this;
    }
}
