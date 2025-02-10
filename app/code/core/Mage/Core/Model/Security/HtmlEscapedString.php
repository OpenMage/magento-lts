<?php
/**
 * Wrapper to escape a string value with a method to get the original string value
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Core
 */
declare(strict_types=1);




class Mage_Core_Model_Security_HtmlEscapedString implements Stringable
{
    protected string $originalValue;

    /**
     * @var string[]|null
     */
    protected ?array $allowedTags;

    /**
     * @param string[]|null $allowedTags
     */
    public function __construct(string $originalValue, ?array $allowedTags = null)
    {
        $this->originalValue = $originalValue;
        $this->allowedTags = $allowedTags;
    }

    /**
     * Get escaped html entities
     */
    public function __toString(): string
    {
        return (string) Mage::helper('core')->escapeHtml(
            $this->originalValue,
            $this->allowedTags,
        );
    }

    /**
     * Get un-escaped html entities
     */
    public function getUnescapedValue(): string
    {
        return $this->originalValue;
    }
}
