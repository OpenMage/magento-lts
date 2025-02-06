<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Oauth
 */

/**
 * OAuth nonce resource collection model
 *
 * @category   Mage
 * @package    Mage_Oauth
 */
class Mage_Oauth_Model_Resource_Nonce_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection model
     */
    protected function _construct()
    {
        $this->_init('oauth/nonce');
    }
}
