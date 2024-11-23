<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Oauth2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

class Mage_Oauth2_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Generate a client secret
     *
     * @param int $length The length of the secret (default: 40)
     * @return string The generated client secret
     * @throws Exception if unable to generate random bytes
     */
    public function generateClientSecret($length = 40)
    {
        return $this->generateRandomString($length);
    }

    /**
     * Generate a token
     *
     * @param int $length The length of the token (default: 40)
     * @return string The generated token
     * @throws Exception if unable to generate random bytes
     */
    public function generateToken($length = 40)
    {
        return $this->generateRandomString($length);
    }

    /**
     * Generate a random string of specified length
     *
     * @param int $length The desired length of the string
     * @return string The generated random string
     * @throws Exception if unable to generate random bytes
     */
    private function generateRandomString($length)
    {
        $bytes = openssl_random_pseudo_bytes((int) ceil($length / 2));
        return substr(bin2hex($bytes), 0, $length);
    }
}
