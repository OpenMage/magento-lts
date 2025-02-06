<?php

declare(strict_types=1);

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Wrapper to escape value und keep the original value
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Security_Obfuscated implements Stringable
{
    protected string $value;

    public function __construct(?string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
