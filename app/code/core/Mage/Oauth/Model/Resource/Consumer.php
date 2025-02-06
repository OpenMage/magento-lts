<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Oauth
 */

/**
 * OAuth Application resource model
 *
 * @category   Mage
 * @package    Mage_Oauth
 */
class Mage_Oauth_Model_Resource_Consumer extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('oauth/consumer', 'entity_id');
    }
}
