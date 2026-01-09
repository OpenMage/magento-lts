<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

use Carbon\Carbon;

/**
 * oAuth nonce resource model
 *
 * @package    Mage_Oauth
 */
class Mage_Oauth_Model_Resource_Nonce extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('oauth/nonce', null);
    }

    /**
     * Delete old entries
     *
     * @param  int                 $minutes Delete entries older than
     * @return int
     * @throws Mage_Core_Exception
     */
    public function deleteOldEntries($minutes)
    {
        if ($minutes > 0) {
            $adapter = $this->_getWriteAdapter();

            return $adapter->delete(
                $this->getMainTable(),
                $adapter->quoteInto('timestamp <= ?', Carbon::now()->subMinutes($minutes)->getTimestamp(), Zend_Db::INT_TYPE),
            );
        }

        return 0;
    }
}
