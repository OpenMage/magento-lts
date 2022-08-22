<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer Review detailed view block
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
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

            $this->setRatingCollection(( $ratingCollection->getSize() ) ? $ratingCollection : false);
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
     * @return boolean
     */
    public function isReviewOwner()
    {
        return ($this->getReviewData()->getCustomerId() == Mage::getSingleton('customer/session')->getCustomerId());
    }
}
