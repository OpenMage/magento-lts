<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Review
 */

/**
 * Review status resource model
 *
 * @category   Mage
 * @package    Mage_Review
 */
class Mage_Review_Model_Resource_Review_Status extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('review/review_status', 'status_id');
    }
}
