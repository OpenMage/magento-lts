<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

/**
 * oAuth nonce model
 *
 * @package    Mage_Oauth
 *
 * @method Mage_Oauth_Model_Resource_Nonce            _getResource()
 * @method Mage_Oauth_Model_Resource_Nonce_Collection getCollection()
 * @method Mage_Oauth_Model_Resource_Nonce            getResource()
 * @method Mage_Oauth_Model_Resource_Nonce_Collection getResourceCollection()
 */
class Mage_Oauth_Model_Nonce extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('oauth/nonce');
    }

    public function getNonce(): string
    {
        return (string) $this->_getData('nonce');
    }

    public function getTimestamp(): string
    {
        return (string) $this->_getData('timestamp');
    }

    public function setNonce(string $value): static
    {
        return $this->setData('nonce', $value);
    }

    public function setTimestamp(string $value): static
    {
        return $this->setData('timestamp', $value);
    }

    /**
     * "After save" actions
     *
     * @return $this
     */
    #[Override]
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
