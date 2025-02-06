<?php

/**
 * @category   Varien
 * @package    Varien_Crypt
 */

/**
 * Crypt factory
 *
 * @category   Varien
 * @package    Varien_Crypt
 */
class Varien_Crypt
{
    /**
     * Factory method to return requested cipher logic
     *
     * @param string $method
     * @return Varien_Crypt_Abstract
     */
    public static function factory($method = 'mcrypt')
    {
        $uc = str_replace(' ', '_', ucwords(str_replace('_', ' ', $method)));
        $className = 'Varien_Crypt_' . $uc;
        return new $className();
    }
}
