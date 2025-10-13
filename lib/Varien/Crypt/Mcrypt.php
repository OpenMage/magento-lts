<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Crypt
 */

/**
 * Mcrypt plugin
 *
 * @package    Varien_Crypt
 */
class Varien_Crypt_Mcrypt extends Varien_Crypt_Abstract
{
    /**
     * Constructor
     */
    public function __construct(array $data = [])
    {
        register_shutdown_function([$this, 'destruct']);
        parent::__construct($data);
    }

    /**
     * Close mcrypt module on shutdown
     */
    public function destruct()
    {
        if ($this->getHandler()) {
            $this->_reset();
        }
    }

    /**
     * Initialize mcrypt module
     *
     * @param string $key cipher private key
     * @return Varien_Crypt_Mcrypt
     */
    public function init($key)
    {
        if (!$this->getCipher()) {
            $this->setCipher(MCRYPT_BLOWFISH);
        }

        if (!$this->getMode()) {
            $this->setMode(MCRYPT_MODE_ECB);
        }

        $this->setHandler(mcrypt_module_open($this->getCipher(), '', $this->getMode(), ''));

        if (!$this->getInitVector()) {
            if (MCRYPT_MODE_CBC == $this->getMode()) {
                $this->setInitVector(substr(
                    md5(mcrypt_create_iv(mcrypt_enc_get_iv_size($this->getHandler()), MCRYPT_RAND)),
                    - mcrypt_enc_get_iv_size($this->getHandler()),
                ));
            } else {
                $this->setInitVector(mcrypt_create_iv(mcrypt_enc_get_iv_size($this->getHandler()), MCRYPT_RAND));
            }
        }

        $maxKeySize = mcrypt_enc_get_key_size($this->getHandler());

        if (strlen($key) > $maxKeySize) { // strlen() intentionally, to count bytes, rather than characters
            $this->setHandler(null);
            throw new Varien_Exception('Maximum key size must be smaller ' . $maxKeySize);
        }

        mcrypt_generic_init($this->getHandler(), $key, $this->getInitVector());

        return $this;
    }

    /**
     * Encrypt data
     *
     * @param string $data source string
     * @return string
     */
    public function encrypt($data)
    {
        if (!$this->getHandler()) {
            throw new Varien_Exception('Crypt module is not initialized.');
        }

        if (strlen($data) == 0) {
            return $data;
        }

        return mcrypt_generic($this->getHandler(), $data);
    }

    /**
     * Decrypt data
     *
     * @param string $data encrypted string
     * @return string
     */
    public function decrypt($data)
    {
        if (!$this->getHandler()) {
            throw new Varien_Exception('Crypt module is not initialized.');
        }

        if (strlen($data) == 0) {
            return $data;
        }

        return mdecrypt_generic($this->getHandler(), $data);
    }

    protected function _reset()
    {
        $handler = $this->getHandler();
        mcrypt_generic_deinit($handler);
        mcrypt_module_close($handler);
    }
}
