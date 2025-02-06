<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Oauth
 */

/**
 * oAuth nonce model
 *
 * @category   Mage
 * @package    Mage_Oauth
 *
 * @method Mage_Oauth_Model_Resource_Nonce getResource()
 * @method Mage_Oauth_Model_Resource_Nonce _getResource()
 * @method string getNonce()
 * @method $this setNonce(string $nonce)
 * @method string getTimestamp()
 * @method $this setTimestamp(string $timestamp)
 */
class Mage_Oauth_Model_Nonce extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('oauth/nonce');
    }

    /**
     * "After save" actions
     *
     * @return $this
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        //Cleanup old entries
        /** @var Mage_Oauth_Helper_Data $helper */
        $helper = Mage::helper('oauth');
        if ($helper->isCleanupProbability()) {
            $this->_getResource()->deleteOldEntries($helper->getCleanupExpirationPeriod());
        }
        return $this;
    }
}
