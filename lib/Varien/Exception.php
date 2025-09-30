<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Exception
 */

class Varien_Exception extends Exception
{
    /**
     * Check PCRE PREG error and throw exception
     *
     * @throws Varien_Exception
     */
    public static function processPcreError()
    {
        if (preg_last_error() != PREG_NO_ERROR) {
            switch (preg_last_error()) {
                case PREG_INTERNAL_ERROR:
                    throw new Varien_Exception('PCRE PREG internal error');
                case PREG_BACKTRACK_LIMIT_ERROR:
                    throw new Varien_Exception('PCRE PREG Backtrack limit error');
                case PREG_RECURSION_LIMIT_ERROR:
                    throw new Varien_Exception('PCRE PREG Recursion limit error');
                case PREG_BAD_UTF8_ERROR:
                    throw new Varien_Exception('PCRE PREG Bad UTF-8 error');
                case PREG_BAD_UTF8_OFFSET_ERROR:
                    throw new Varien_Exception('PCRE PREG Bad UTF-8 offset error');
            }
        }
    }
}
