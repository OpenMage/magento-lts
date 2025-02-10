<?php
/**
 * Wrapper to escape value und keep the original value
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Core
 */
declare(strict_types=1);




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
