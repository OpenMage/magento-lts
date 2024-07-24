<?php
declare(strict_types=1);

/**
 *
 */
class Mage_Core_Model_Security_HtmlEscapedString implements Stringable
{

    protected $originalValue;
    protected $allowedTags;

    /**
     * @param string $originalValue
     * @param string[]|null $allowedTags
     */
    public function __construct(string $originalValue, ?array $allowedTags = null)
    {
        $this->originalValue = $originalValue;
        $this->allowedTags = $allowedTags;
    }

    public function __toString(): string
    {
        return (string) Mage::helper('core')->escapeHtml(
            $this->originalValue,
            $this->allowedTags
        );
    }

    public function getUnescapedValue(): string
    {
        return $this->originalValue;
    }
}
