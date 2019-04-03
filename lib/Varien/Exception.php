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
 * @package     Varien_Exception
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
