<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Varien
 * @package    Varien_Crypt
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mcrypt plugin
 *
 * @category   Varien
 * @package    Varien_Crypt
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Varien_Crypt_Mcrypt extends Varien_Crypt_Abstract
{
    /**
     * Constuctor
     *
     * @param array $data
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
                    - mcrypt_enc_get_iv_size($this->getHandler())
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
        mcrypt_generic_deinit($this->getHandler());
        mcrypt_module_close($this->getHandler());
    }
}
