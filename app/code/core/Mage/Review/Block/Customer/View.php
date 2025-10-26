<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Review
 */

/**
 * Customer Review detailed view block
 *
 * @package    Mage_Review
 *
 * @method string getReviewId()
 * @method $this setReviewId(string $value)
 * @method Mage_Catalog_Model_Product getProductCacheData()
 * @method $this setProductCacheData(Mage_Catalog_Model_Product $value)
 * @method Mage_Rating_Model_Resource_Rating_Option_Vote_Collection|false getRatingCollection()
 * @method $this setRatingCollection(Mage_Rating_Model_Resource_Rating_Option_Vote_Collection|false $value)
 * @method array getRatingSummaryCache()
 * @method setRatingSummaryCache(array $value)
 * @method Mage_Review_Model_Review getReviewCachedData()
 * @method $this setReviewCachedData(Mage_Review_Model_Review $value)
 * @method int getTotalReviewsCache()
 * @method $this setTotalReviewsCache(int $entityPkValue, bool $approvedOnly, int $storeId)
 */
class Mage_Review_Block_Customer_View extends Mage_Catalog_Block_Product_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('review/customer/view.phtml');

        $this->setReviewId($this->getRequest()->getParam('id', false));
    }

    /**
     * @return Mage_Catalog_Model_Product
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getProductData()
    {
        if ($this->getReviewId() && !$this->getProductCacheData()) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($this->getReviewData()->getEntityPkValue());
            $this->setProductCacheData($product);
        }

        return $this->getProductCacheData();
    }

    /**
     * @return Mage_Review_Model_Review
     */
    public function getReviewData()
    {
        if ($this->getReviewId() && !$this->getReviewCachedData()) {
            $this->setReviewCachedData(Mage::getModel('review/review')->load($this->getReviewId()));
        }

        return $this->getReviewCachedData();
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return Mage::getUrl('review/customer');
    }

    /**
     * @return Mage_Rating_Model_Resource_Rating_Option_Vote_Collection
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getRating()
    {
        if (!$this->getRatingCollection()) {
            $ratingCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setReviewFilter($this->getReviewId())
                ->addRatingInfo(Mage::app()->getStore()->getId())
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->load();

            $this->setRatingCollection(($ratingCollection->getSize()) ? $ratingCollection : false);
        }

        return $this->getRatingCollection();
    }

    /**
     * @return array
     */
    public function getRatingSummary()
    {
        if (!$this->getRatingSummaryCache()) {
            $this->setRatingSummaryCache(Mage::getModel('rating/rating')->getEntitySummary($this->getProductData()->getId()));
        }

        return $this->getRatingSummaryCache();
    }

    /**
     * @return int
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getTotalReviews()
    {
        if (!$this->getTotalReviewsCache()) {
            $this->setTotalReviewsCache(Mage::getModel('review/review')->getTotalReviews($this->getProductData()->getId()), false, Mage::app()->getStore()->getId());
        }

        return $this->getTotalReviewsCache();
    }

    /**
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_LONG);
    }

    /**
     * Check whether current customer is review owner
     *
     * @return bool
     */
    public function isReviewOwner()
    {
        return ($this->getReviewData()->getCustomerId() == Mage::getSingleton('customer/session')->getCustomerId());
    }
}
