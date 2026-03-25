<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rating
 */

/**
 * Rating vote model
 *
 * @package    Mage_Rating
 *
 * @method Mage_Rating_Model_Resource_Rating_Option_Vote            _getResource()
 * @method Mage_Rating_Model_Resource_Rating_Option_Vote_Collection getCollection()
 * @method string                                                   getEntityPkValue()
 * @method int                                                      getRatingId()
 * @method Mage_Rating_Model_Resource_Rating_Option_Vote            getResource()
 * @method Mage_Rating_Model_Resource_Rating_Option_Vote_Collection getResourceCollection()
 * @method $this                                                    setRatingOptions(Mage_Rating_Model_Resource_Rating_Option_Collection $options)
 */
class Mage_Rating_Model_Rating_Option_Vote extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('rating/rating_option_vote');
    }
}
