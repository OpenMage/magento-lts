<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

/**
 * OAuth Application resource model
 *
 * @package    Mage_Oauth
 */
class Mage_Oauth_Model_Resource_Consumer extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('oauth/consumer', 'entity_id');
    }
}
