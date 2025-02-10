<?php
/**
 * oAuth nonce resource model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Oauth
 */
class Mage_Oauth_Model_Resource_Nonce extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('oauth/nonce', null);
    }

    /**
     * Delete old entries
     *
     * @param int $minutes Delete entries older than
     * @return int
     */
    public function deleteOldEntries($minutes)
    {
        if ($minutes > 0) {
            $adapter = $this->_getWriteAdapter();

            return $adapter->delete(
                $this->getMainTable(),
                $adapter->quoteInto('timestamp <= ?', time() - $minutes * 60, Zend_Db::INT_TYPE),
            );
        } else {
            return 0;
        }
    }
}
