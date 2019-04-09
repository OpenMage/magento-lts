<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Varien
 * @package     Varien_Crypt
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Mcrypt plugin
 *
 * @category   Varien
 * @package    Varien_Crypt
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Crypt_Mcrypt extends Varien_Crypt_Abstract
{
    /**
     * Constuctor
     *
     * @param array $data
     */
    public function __construct(array $data=array())
    {
        register_shutdown_function(array($this, 'destruct'));
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
	 * Transition table
	 */
    protected function _getCipher()
    {
        $transition = [
            (defined('MCRYPT_BLOWFISH') ? MCRYPT_BLOWFISH : 'blowfish') => 'bf-ecb',
            // more constants need to be implement here
        ];

        if (!array_key_exists($this->getCipher(), $transition)) {
            return NULL;
        }

        return $transition[$this->getCipher()];
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
            $this->setCipher(defined('MCRYPT_BLOWFISH') ? MCRYPT_BLOWFISH : 'blowfish');
        }

        if (!in_array($this->_getCipher(), openssl_get_cipher_methods())) {
            throw new Varien_Exception('Cipher ' . $this->_getCipher() . ' is not supported by openssl');
        }

        if (!$this->getInitVector()) {
            $ivlen = openssl_cipher_iv_length($this->_getCipher());
            $iv = openssl_random_pseudo_bytes($ivlen);

            $this->setInitVector($iv);
        }

        $this->setHandler(true);
        $this->setKey($key);

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

        return openssl_encrypt($data, $this->_getCipher(), $this->getKey(), 0, $this->getInitVector());
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

        return openssl_decrypt($data, $this->_getCipher(), $this->getKey(), 0, $this->getInitVector());
    }

    protected function _reset()
    {
        // unused as PHP 7.3
    }
}
