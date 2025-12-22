<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rating
 */

/**
 * Rating option collection
 *
 * @package    Mage_Rating
 */
class Mage_Rating_Model_Resource_Rating_Option_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Rating options table
     *
     * @var string
     * @deprecated since 1.5.0.0
     */
    protected $_ratingOptionTable;

    /**
     * Rating votes table
     *
     * @var string
     */
    protected $_ratingVoteTable;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('rating/rating_option');
        $this->_ratingOptionTable   = $this->getTable('rating/rating_option');
        $this->_ratingVoteTable     = $this->getTable('rating/rating_option_vote');
    }

    /**
     * Add rating filter
     *
     * @param  array|int $rating
     * @return $this
     */
    public function addRatingFilter($rating)
    {
        if (is_numeric($rating)) {
            $this->addFilter('rating_id', $rating);
        } elseif (is_array($rating)) {
            $this->addFilter('rating_id', $this->_getConditionSql('rating_id', ['in' => $rating]), 'string');
        }

        return $this;
    }

    /**
     * Set order by position field
     *
     * @param  string $dir
     * @return $this
     */
    public function setPositionOrder($dir = 'ASC')
    {
        $this->setOrder('main_table.position', $dir);
        return $this;
    }
}
