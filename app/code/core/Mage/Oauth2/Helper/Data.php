<?php

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
     * @param string $clientId The client ID
     * @param int $length The length of the token (default: 40)
     * @return string The generated token
     * @throws Exception if unable to generate random bytes
     */
    public function generateToken($clientId, $length = 40)
    {
        $randomPart = $this->generateRandomString($length - 8);
        $clientPart = substr(hash('crc32b', $clientId), 0, 8);
        return $randomPart . $clientPart;
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
        $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
        return substr(bin2hex($bytes), 0, $length);
    }
}
