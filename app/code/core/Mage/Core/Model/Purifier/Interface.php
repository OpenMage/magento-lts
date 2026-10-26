<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

declare(strict_types=1);

/**
 * A model for purifying HTML strings.
 *
 * "Purify" in this context means to sanitize HTML in order to prevent
 * vulnerabilities and/or broken HTML. It is also a reference to the
 * HTMLPurifier library that is used as a dependency in the default
 * implementation.
 *
 * @package Mage_Core
 */
interface Mage_Core_Model_Purifier_Interface
{
    /**
     * Purify Html Content
     */
    public function purify(string $html): string;
}
