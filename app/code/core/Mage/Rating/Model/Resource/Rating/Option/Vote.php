<?php
/**
 * Rating vote resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Rating
 */
class Mage_Rating_Model_Resource_Rating_Option_Vote extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('rating/rating_option_vote', 'vote_id');
    }
}
