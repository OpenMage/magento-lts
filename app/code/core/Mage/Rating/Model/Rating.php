<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rating
 */

/**
 * Rating model
 *
 * @package    Mage_Rating
 *
 * @method Mage_Rating_Model_Resource_Rating            _getResource()
 * @method Mage_Rating_Model_Resource_Rating_Collection getCollection()
 * @method string                                       getEntityPkValue()
 * @method string                                       getRatingCode()
 * @method array                                        getRatingCodes()
 * @method Mage_Rating_Model_Resource_Rating            getResource()
 * @method Mage_Rating_Model_Resource_Rating_Collection getResourceCollection()
 * @method int                                          getReviewId()
 * @method int                                          getStoreId()
 * @method array                                        getStores()
 * @method int                                          getVoteId()
 * @method bool                                         hasRatingCodes()
 * @method bool                                         hasStores()
 * @method $this                                        setCount(int $value)
 * @method $this                                        setCustomerId(int $value)
 * @method $this                                        setEntityId(int $value)
 * @method $this                                        setEntityPkValue(string $value)
 * @method $this                                        setId(string $value)
 * @method $this                                        setPosition(string $value)
 * @method $this                                        setRatingCode(string $value)
 * @method $this                                        setRatingCodes(array $value)
 * @method $this                                        setRatingId(int $value)
 * @method $this                                        setReviewId(int $value)
 * @method $this                                        setStoreId(int $value)
 * @method $this                                        setStores(array $value)
 * @method $this                                        setSum(int $value)
 * @method $this                                        setSummary(float|int $param)
 */
class Mage_Rating_Model_Rating extends Mage_Core_Model_Abstract
{
    /**
     * rating entity codes
     */
    public const ENTITY_PRODUCT_CODE           = 'product';

    public const ENTITY_PRODUCT_REVIEW_CODE    = 'product_review';

    public const ENTITY_REVIEW_CODE            = 'review';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('rating/rating');
    }

    /**
     * @param  int    $optionId
     * @param  string $entityPkValue
     * @param  int    $customerId
     * @return $this
     */
    public function addOptionVote($optionId, $entityPkValue, $customerId = null)
    {
        Mage::getModel('rating/rating_option')->setOptionId($optionId)
            ->setRatingId($this->getId())
            ->setReviewId($this->getReviewId())
            ->setEntityPkValue($entityPkValue)
            ->setCustomerId($customerId)
            ->addVote();
        return $this;
    }

    /**
     * @param  int   $optionId
     * @return $this
     */
    public function updateOptionVote($optionId)
    {
        Mage::getModel('rating/rating_option')->setOptionId($optionId)
            ->setVoteId($this->getVoteId())
            ->setReviewId($this->getReviewId())
            ->setDoUpdate(1)
            ->addVote();
        return $this;
    }

    /**
     * retrieve rating options
     *
     * @return array
     */
    public function getOptions()
    {
        if ($options = $this->getData('options')) {
            return $options;
        }

        if ($id = $this->getId()) {
            return Mage::getResourceModel('rating/rating_option_collection')
               ->addRatingFilter($id)
               ->setPositionOrder()
               ->load()
               ->getItems();
        }

        return [];
    }

    /**
     * Get rating collection object
     *
     * @param  string                         $entityPkValue
     * @param  bool                           $onlyForCurrentStore
     * @return array|Mage_Rating_Model_Rating
     */

    public function getEntitySummary($entityPkValue, $onlyForCurrentStore = true)
    {
        $this->setEntityPkValue($entityPkValue);
        return $this->_getResource()->getEntitySummary($this, $onlyForCurrentStore);
    }

    /**
     * @param  int   $reviewId
     * @param  bool  $onlyForCurrentStore
     * @return array
     */
    public function getReviewSummary($reviewId, $onlyForCurrentStore = true)
    {
        $this->setReviewId($reviewId);
        return $this->_getResource()->getReviewSummary($this, $onlyForCurrentStore);
    }

    /**
     * Get rating entity type id by code
     *
     * @param  string $entityCode
     * @return string
     */
    public function getEntityIdByCode($entityCode)
    {
        return $this->getResource()->getEntityIdByCode($entityCode);
    }
}
