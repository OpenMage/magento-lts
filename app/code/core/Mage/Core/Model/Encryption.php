<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Provides basic logic for hashing passwords and encrypting/decrypting misc data
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Encryption
{
    public const HASH_VERSION_MD5    = 0;
    public const HASH_VERSION_SHA256 = 1;
    public const HASH_VERSION_SHA512 = 2;

    /**
     * Encryption method bcrypt
     */
    public const HASH_VERSION_LATEST = 3;

    /**
     * Maximum Password Length
     */
    public const MAXIMUM_PASSWORD_LENGTH = 256;

    /**
     * @var Varien_Crypt_Mcrypt
     */
    protected $_crypt;

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    /**
     * Set helper instance
     *
     * @param Mage_Core_Helper_Data $helper
     * @return $this
     */
    public function setHelper($helper)
    {
        $this->_helper = $helper;
        return $this;
    }

    /**
     * Generate a [salted] hash.
     *
     * $salt can be:
     * false - a random will be generated
     * integer - a random with specified length will be generated
     * string
     *
     * @param string $password
     * @param mixed $salt
     * @return string
     */
    public function getHash($password, $salt = false)
    {
        if (is_int($salt)) {
            $salt = $this->_helper->getRandomString($salt);
        }
        return $salt === false
            ? $this->hash($password)
            : $this->hash($salt . $password, self::HASH_VERSION_SHA256) . ':' . $salt;
    }

    /**
     * Generate hash for customer password
     *
     * @param string $password
     * @param mixed $salt
     * @return string
     */
    public function getHashPassword($password, $salt = null)
    {
        if (is_int($salt)) {
            $salt = $this->_helper->getRandomString($salt);
        }
        return (bool) $salt
            ? $this->hash($salt . $password, $this->_helper->getVersionHash($this)) . ':' . $salt
            : $this->hash($password, $this->_helper->getVersionHash($this));
    }

    /**
     * Hash a string
     *
     * @param string $data
     * @param int $version
     * @return bool|string
     */
    public function hash($data, $version = self::HASH_VERSION_MD5)
    {
        if (self::HASH_VERSION_LATEST === $version && $version === $this->_helper->getVersionHash($this)) {
            return password_hash($data, PASSWORD_DEFAULT);
        } elseif (self::HASH_VERSION_SHA256 == $version) {
            return hash('sha256', $data);
        } elseif (self::HASH_VERSION_SHA512 == $version) {
            return hash('sha512', $data);
        }
        return md5($data);
    }

    /**
     * Validate hash against hashing method (with or without salt)
     *
     * @param string $password
     * @param string $hash
     * @return bool
     * @throws Exception
     */
    public function validateHash($password, $hash)
    {
        if (strlen($password) > self::MAXIMUM_PASSWORD_LENGTH) {
            return false;
        }

        return $this->validateHashByVersion($password, $hash, self::HASH_VERSION_LATEST)
            || $this->validateHashByVersion($password, $hash, self::HASH_VERSION_SHA512)
            || $this->validateHashByVersion($password, $hash, self::HASH_VERSION_SHA256)
            || $this->validateHashByVersion($password, $hash, self::HASH_VERSION_MD5);
    }

    /**
     * Validate hash by specified version
     *
     * @param string $password
     * @param string $hash
     * @param int $version
     * @return bool
     */
    public function validateHashByVersion($password, $hash, $version = self::HASH_VERSION_MD5)
    {
        if ($version == self::HASH_VERSION_LATEST && $version == $this->_helper->getVersionHash($this)) {
            return password_verify($password, $hash);
        }
        // look for salt
        $hashArr = explode(':', $hash, 2);
        if (count($hashArr) === 1) {
            return hash_equals($this->hash($password, $version), $hash);
        }
        list($hash, $salt) = $hashArr;
        return hash_equals($this->hash($salt . $password, $version), $hash);
    }

    /**
     * Instantiate crypt model
     *
     * @param string $key
     * @return Varien_Crypt_Mcrypt
     */
    protected function _getCrypt($key = null)
    {
        if (!$this->_crypt) {
            if ($key === null) {
                $key = (string)Mage::getConfig()->getNode('global/crypt/key');
            }
            $this->_crypt = Varien_Crypt::factory()->init($key);
        }
        return $this->_crypt;
    }

    /**
     * Encrypt a string
     *
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        return base64_encode($this->_getCrypt()->encrypt((string)$data));
    }

    /**
     * Decrypt a string
     *
     * @param string $data
     * @return string
     */
    public function decrypt($data)
    {
        return str_replace("\x0", '', trim($this->_getCrypt()->decrypt(base64_decode((string)$data))));
    }

    /**
     * Return crypt model, instantiate if it is empty
     *
     * @param string $key
     * @return Varien_Crypt_Mcrypt
     */
    public function validateKey($key)
    {
        return $this->_getCrypt($key);
    }
}
