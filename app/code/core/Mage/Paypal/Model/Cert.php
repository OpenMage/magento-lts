<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

use Carbon\Carbon;

/**
 * PayPal specific model for certificate based authentication
 *
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_Cert extends Mage_Core_Model_Abstract
{
    /**
     * Certificate base path
     */
    public const BASEPATH_PAYPAL_CERT  = 'cert/paypal';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('paypal/cert');
    }

    /**
     * Load model by website id
     *
     * @param  int   $websiteId
     * @param  bool  $strictLoad
     * @return $this
     */
    public function loadByWebsite($websiteId, $strictLoad = true)
    {
        $this->setWebsiteId($websiteId);
        $this->_getResource()->loadByWebsite($this, $strictLoad);
        return $this;
    }

    /**
     * Get path to PayPal certificate file, if file does not exist try to create it
     *
     * @return string
     */
    public function getCertPath()
    {
        if (!$this->getContent()) {
            Mage::throwException(Mage::helper('paypal')->__('PayPal certificate does not exist.'));
        }

        $certFileName = sprintf('cert_%s_%s.pem', $this->getWebsiteId(), Carbon::parse($this->getUpdatedAt())->getTimestamp());
        $certFile = $this->_getBaseDir() . DS . $certFileName;

        if (!file_exists($certFile)) {
            $this->_createCertFile($certFile);
        }

        return $certFile;
    }

    /**
     * Create physical certificate file based on DB data
     *
     * @param string $file
     */
    protected function _createCertFile($file)
    {
        $certDir = $this->_getBaseDir();
        if (!is_dir($certDir)) {
            $ioAdapter = new Varien_Io_File();
            $ioAdapter->checkAndCreateFolder($certDir);
        } else {
            $this->_removeOutdatedCertFile();
        }

        file_put_contents($file, Mage::helper('core')->decrypt($this->getContent()));
    }

    /**
     * Check and remove outdated certificate file by website
     */
    protected function _removeOutdatedCertFile()
    {
        $certDir = $this->_getBaseDir();
        if (is_dir($certDir)) {
            $entries = scandir($certDir);
            foreach ($entries as $entry) {
                if ($entry != '.' && $entry != '..' && str_contains($entry, 'cert_' . $this->getWebsiteId())) {
                    unlink($certDir . DS . $entry);
                }
            }
        }
    }

    /**
     * Retrieve base directory for certificate
     *
     * @return string
     */
    protected function _getBaseDir()
    {
        return Mage::getBaseDir('var') . DS . self::BASEPATH_PAYPAL_CERT;
    }

    /**
     * Delete assigned certificate file after delete object
     *
     * @return $this
     */
    protected function _afterDelete()
    {
        $this->_removeOutdatedCertFile();
        return $this;
    }
}
