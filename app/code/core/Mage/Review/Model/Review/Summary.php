<?php
/**
 * Review summary
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Review
 * @method $this setStoreId(int $value)
 */
class Mage_Review_Model_Review_Summary extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('review/review_summary');
    }

    /**
     * @return string
     */
    public function getEntityPkValue()
    {
        return $this->_getData('entity_pk_value');
    }

    /**
     * @return array
     */
    public function getRatingSummary()
    {
        return $this->_getData('rating_summary');
    }

    /**
     * @return int
     */
    public function getReviewsCount()
    {
        return $this->_getData('reviews_count');
    }
}
