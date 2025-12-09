<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ConfigurableSwatches
 */

/**
 * Wrapper to modify a string value with a method to get the original string value
 *
 * @package    Mage_ConfigurableSwatches
 */
class Mage_Core_Model_String_Normalized implements Stringable
{
    protected ?string $originalValue;

    public function __construct(?string $originalValue)
    {
        $this->originalValue = $originalValue;
    }

    /**
     * Get normalized string value
     */
    public function __toString(): string
    {
        if ($this->originalValue === null || $this->originalValue === '') {
            return '';
        }

        if (function_exists('mb_strtolower')) {
            return trim(mb_strtolower($this->originalValue, 'UTF-8'));
        }

        return trim(strtolower($this->originalValue));
    }

    /**
     * Get non-nomalized original value
     */
    public function getOriginalValue(): ?string
    {
        return $this->originalValue;
    }
}
