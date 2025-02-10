<?php
/**
 * OAuth Application resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Oauth
 */
class Mage_Oauth_Model_Resource_Consumer extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('oauth/consumer', 'entity_id');
    }
}
