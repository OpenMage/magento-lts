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
 * Review model
 *
 * @method Mage_Review_Model_Resource_Review _getResource()
 * @method Mage_Review_Model_Resource_Review getResource()
 * @method Mage_Review_Model_Resource_Review_Collection getCollection()
 *
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 * @method array getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method string getDetail()
 * @method $this setEntityId(int $value)
 * @method int getEntityPkValue()
 * @method $this setEntityPkValue(int $value)
 * @method string getNickname()
 * @method $this setRatingVotes(Mage_Rating_Model_Resource_Rating_Option_Vote_Collection $collection)
 * @method int getReviewId()
 * @method int getStatusId()
 * @method $this setStatusId(int $value)
 * @method $this setStoreId(int $value)
 * @method int getStoreId()
 * @method array getStores()
 * @method $this setStores(array $value)
 * @method string getTitle()
 *
 * @category    Mage
 * @package     Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Review_Model_Review extends Mage_Core_Model_Abstract
{
    /**
     * Event prefix for observer
     *
     * @var string
     */
    protected $_eventPrefix = 'review';

    /**
     * @deprecated after 1.3.2.4
     *
     */
    const ENTITY_PRODUCT = 1;

    /**
     * Review entity codes
     *
     */
    const ENTITY_PRODUCT_CODE   = 'product';
    const ENTITY_CUSTOMER_CODE  = 'customer';
    const ENTITY_CATEGORY_CODE  = 'category';

    const STATUS_APPROVED       = 1;
    const STATUS_PENDING        = 2;
    const STATUS_NOT_APPROVED   = 3;

    protected function _construct()
    {
        $this->_init('review/review');
    }

    /**
     * @return Mage_Review_Model_Resource_Review_Product_Collection
     */
    public function getProductCollection()
    {
        return Mage::getResourceModel('review/review_product_collection');
    }

    /**
     * @return Mage_Review_Model_Resource_Review_Status_Collection
     */
    public function getStatusCollection()
    {
        return Mage::getResourceModel('review/review_status_collection');
    }

    /**
     * @param int $entityPkValue
     * @param bool $approvedOnly
     * @param int $storeId
     * @return int
     */
    public function getTotalReviews($entityPkValue, $approvedOnly = false, $storeId = 0)
    {
        return $this->getResource()->getTotalReviews($entityPkValue, $approvedOnly, $storeId);
    }

    /**
     * @return $this
     */
    public function aggregate()
    {
        $this->getResource()->aggregate($this);
        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param int $storeId
     */
    public function getEntitySummary($product, $storeId = 0)
    {
        $summaryData = Mage::getModel('review/review_summary')
            ->setStoreId($storeId)
            ->load($product->getId());
        $summary = new Varien_Object();
        $summary->setData($summaryData->getData());
        $product->setRatingSummary($summary);
    }

    /**
     * @return int
     */
    public function getPendingStatus()
    {
        return self::STATUS_PENDING;
    }

    /**
     * @return string
     */
    public function getReviewUrl()
    {
        return Mage::getUrl('review/product/view', ['id' => $this->getReviewId()]);
    }

    /**
     * @return array|bool
     * @throws Zend_Validate_Exception
     */
    public function validate()
    {
        $errors = [];

        if (!Zend_Validate::is($this->getTitle(), 'NotEmpty')) {
            $errors[] = Mage::helper('review')->__('Review summary can\'t be empty');
        }

        if (!Zend_Validate::is($this->getNickname(), 'NotEmpty')) {
            $errors[] = Mage::helper('review')->__('Nickname can\'t be empty');
        }

        if (!Zend_Validate::is($this->getDetail(), 'NotEmpty')) {
            $errors[] = Mage::helper('review')->__('Review can\'t be empty');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Perform actions after object delete
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterDeleteCommit()
    {
        $this->getResource()->afterDeleteCommit($this);
        return parent::_afterDeleteCommit();
    }

    /**
     * Append review summary to product collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return $this
     */
    public function appendSummary($collection)
    {
        $entityIds = [];
        foreach ($collection->getItems() as $_itemId => $_item) {
            $entityIds[] = $_item->getId();
        }

        if (!count($entityIds)) {
            return $this;
        }

        $summaryData = Mage::getResourceModel('review/review_summary_collection')
            ->addEntityFilter($entityIds)
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->load();

        /** @var Mage_Review_Model_Review_Summary $_summary */
        foreach ($summaryData as $_summary) {
            if (($_item = $collection->getItemById($_summary->getEntityPkValue()))) {
                $_item->setRatingSummary($_summary);
            }
        }

        return $this;
    }

    /**
     * @return Mage_Core_Model_Abstract
     * @throws Mage_Core_Exception
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * Check if current review approved or not
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->getStatusId() == self::STATUS_APPROVED;
    }

    /**
     * Check if current review available on passed store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isAvailableOnStore($store = null)
    {
        $store = Mage::app()->getStore($store);
        if ($store) {
            return in_array($store->getId(), (array)$this->getStores());
        }

        return false;
    }

    /**
     * Get review entity type id by code
     *
     * @param string $entityCode
     * @return int|bool
     */
    public function getEntityIdByCode($entityCode)
    {
        return $this->getResource()->getEntityIdByCode($entityCode);
    }
}
