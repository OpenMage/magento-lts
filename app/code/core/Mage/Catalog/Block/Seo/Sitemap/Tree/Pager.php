<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Html page block
 *
 * @package    Mage_Catalog
 * @todo       Separate order, mode and pager
 */
class Mage_Catalog_Block_Seo_Sitemap_Tree_Pager extends Mage_Page_Block_Html_Pager
{
    protected $_showPerPage     = false;
    protected $_lastPageNumber  = 1;
    protected $_totalNum        = 0;
    protected $_firstNum        = 0;
    protected $_lastNum         = 1;

    /**
     * @param int $displacement
     * @return int
     * @throws Exception
     */
    public function getCurrentPage($displacement = 0)
    {
        if ($page = (int) $this->getRequest()->getParam($this->getPageVarName()) + $displacement) {
            if ($page > $this->getLastPageNum()) {
                return $this->getLastPageNum();
            }
            return $page;
        }
        return 1;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        $limits = $this->getAvailableLimit();
        $limits = array_keys($limits);
        return $limits[0];
    }

    /**
     * @param Varien_Data_Collection $collection
     * @return $this|Mage_Page_Block_Html_Pager
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    /**
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * @return int
     */
    public function getFirstNum()
    {
        return $this->_firstNum + 1;
    }

    /**
     * @param int $firstNum
     * @return $this
     */
    public function setFirstNum($firstNum)
    {
        $this->_firstNum = $firstNum;
        return $this;
    }

    /**
     * @return int
     */
    public function getLastNum()
    {
        return $this->_lastNum;
    }

    /**
     * @param int $lastNum
     * @return $this
     */
    public function setLastNum($lastNum)
    {
        $this->_lastNum = $lastNum;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalNum()
    {
        return $this->_totalNum;
    }

    /**
     * @param int $totalNum
     * @return $this
     */
    public function setTotalNum($totalNum)
    {
        $this->_totalNum = $totalNum;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->getCurrentPage() == 1;
    }

    /**
     * @return int
     */
    public function getLastPageNum()
    {
        return $this->_lastPageNumber;
    }

    /**
     * @param int $lastPageNum
     * @return $this
     */
    public function setLastPageNum($lastPageNum)
    {
        $this->_lastPageNumber = $lastPageNum;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLastPage()
    {
        return $this->getCurrentPage() >= $this->getLastPageNum();
    }

    /**
     * @return array
     */
    public function getPages()
    {
        $start = 1;
        $finish = 1;

        if ($this->getLastPageNum() <= $this->_displayPages) {
            $pages = range(1, $this->getLastPageNum());
        } else {
            $half = ceil($this->_displayPages / 2);
            if ($this->getCurrentPage() >= $half && $this->getCurrentPage() <= $this->getLastPageNum() - $half) {
                $start  = ($this->getCurrentPage() - $half) + 1;
                $finish = ($start + $this->_displayPages) - 1;
            } elseif ($this->getCurrentPage() < $half) {
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
     * @return string
     */
    public function getPreviousPageUrl()
    {
        return $this->getPageUrl($this->getCurrentPage(-1));
    }

    /**
     * @return string
     */
    public function getNextPageUrl()
    {
        return $this->getPageUrl($this->getCurrentPage(+1));
    }

    /**
     * @return string
     */
    public function getLastPageUrl()
    {
        return $this->getPageUrl($this->getLastPageNum());
    }
}
