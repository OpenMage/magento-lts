<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */

/**
 * Downloadable Product link purchased resource model
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Model_Resource_Link_Purchased extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('downloadable/link_purchased', 'purchased_id');
    }
}
