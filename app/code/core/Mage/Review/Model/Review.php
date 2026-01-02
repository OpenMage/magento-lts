<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Review
 */

/**
 * Review model
 *
 * @package    Mage_Review
 *
 * @method Mage_Review_Model_Resource_Review            _getResource()
 * @method Mage_Review_Model_Resource_Review_Collection getCollection()
 * @method array                                        getCustomerId()
 * @method string                                       getDetail()
 * @method int                                          getEntityPkValue()
 * @method string                                       getNickname()
 * @method Mage_Review_Model_Resource_Review            getResource()
 * @method Mage_Review_Model_Resource_Review_Collection getResourceCollection()
 * @method int                                          getReviewId()
 * @method int                                          getStatusId()
 * @method int                                          getStoreId()
 * @method array                                        getStores()
 * @method string                                       getTitle()
 * @method $this                                        setCustomerId(int $value)
 * @method $this                                        setEntityId(int $value)
 * @method $this                                        setEntityPkValue(int $value)
 * @method $this                                        setRatingVotes(Mage_Rating_Model_Resource_Rating_Option_Vote_Collection $collection)
 * @method $this                                        setStatusId(int $value)
 * @method $this                                        setStoreId(int $value)
 * @method $this                                        setStores(array $value)
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
     */
    public const ENTITY_PRODUCT = 1;

    /**
     * Review entity codes
     */
    public const ENTITY_PRODUCT_CODE   = 'product';

    public const ENTITY_CUSTOMER_CODE  = 'customer';

    public const ENTITY_CATEGORY_CODE  = 'category';

    public const STATUS_APPROVED       = 1;

    public const STATUS_PENDING        = 2;

    public const STATUS_NOT_APPROVED   = 3;

    /**
     * @inheritDoc
     */
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
     * @param  int    $entityPkValue
     * @param  bool   $approvedOnly
     * @param  int    $storeId
     * @return string
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
     * @param int                        $storeId
     */
    public function getEntitySummary($product, $storeId = 0)
    {
        $product->setRatingSummary($product->getReviewSummary($storeId));
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
     * @return array|true
     */
    public function validate()
    {
        $validator  = $this->getValidationHelper();
        $violations = new ArrayObject();

        $violations->append($validator->validateNotEmpty(
            value: $this->getTitle(),
            message: Mage::helper('review')->__("Review summary can't be empty"),
        ));

        $violations->append($validator->validateNotEmpty(
            value: $this->getNickname(),
            message: Mage::helper('review')->__("Nickname can't be empty"),
        ));

        $violations->append($validator->validateNotEmpty(
            value: $this->getDetail(),
            message: Mage::helper('review')->__("Review can't be empty"),
        ));

        $errors = $validator->getErrorMessages($violations);
        if (!$errors) {
            return true;
        }

        return (array) $errors;
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
     * @param  Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    public function appendSummary($collection)
    {
        $entityIds = [];
        foreach ($collection->getItems() as $item) {
            $entityIds[] = $item->getId();
        }

        if ($entityIds === []) {
            return $this;
        }

        $summaryData = Mage::getResourceModel('review/review_summary_collection')
            ->addEntityFilter($entityIds)
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->load();

        /** @var Mage_Review_Model_Review_Summary $summary */
        foreach ($summaryData as $summary) {
            if (($item = $collection->getItemById($summary->getEntityPkValue()))) {
                $item->setRatingSummary($summary);
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
     * @param  int|Mage_Core_Model_Store       $store
     * @return bool
     * @throws Mage_Core_Model_Store_Exception
     */
    public function isAvailableOnStore($store = null)
    {
        $store = Mage::app()->getStore($store);
        if ($store) {
            return in_array($store->getId(), (array) $this->getStores());
        }

        return false;
    }

    /**
     * Get review entity type id by code
     *
     * @param  string   $entityCode
     * @return bool|int
     */
    public function getEntityIdByCode($entityCode)
    {
        return $this->getResource()->getEntityIdByCode($entityCode);
    }
}
