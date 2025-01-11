<?php

declare(strict_types=1);

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wrapper to escape a string value with a method to get the original string value
 *
 * @category   Mage
 * @package    Mage_Core
 */
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
