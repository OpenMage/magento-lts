<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rating
 */

/**
 * Rating option model
 *
 * @package    Mage_Rating
 *
 * @method Mage_Rating_Model_Resource_Rating_Option            _getResource()
 * @method string                                              getCode()
 * @method Mage_Rating_Model_Resource_Rating_Option_Collection getCollection()
 * @method int                                                 getDoUpdate()
 * @method string                                              getEntityPkValue()
 * @method int                                                 getPosition()
 * @method int                                                 getRatingId()
 * @method Mage_Rating_Model_Resource_Rating_Option            getResource()
 * @method Mage_Rating_Model_Resource_Rating_Option_Collection getResourceCollection()
 * @method int                                                 getReviewId()
 * @method int                                                 getValue()
 * @method int                                                 getVoteId()
 * @method $this                                               setCode(string $value)
 * @method $this                                               setDoUpdate(int $value)
 * @method $this                                               setEntityPkValue(string $value)
 * @method $this                                               setOptionId(int $value)
 * @method $this                                               setPosition(int $value)
 * @method $this                                               setRatingId(int $value)
 * @method $this                                               setReviewId(int $value)
 * @method $this                                               setValue(int $value)
 * @method $this                                               setVoteId(int $value)
 */
class Mage_Rating_Model_Rating_Option extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('rating/rating_option');
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function addVote()
    {
        $this->getResource()->addVote($this);
        return $this;
    }

    /**
     * @param  int   $id
     * @return $this
     */
    public function setId($id)
    {
        $this->setOptionId($id);
        return $this;
    }
}
