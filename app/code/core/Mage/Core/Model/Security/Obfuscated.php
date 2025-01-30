<?php

declare(strict_types=1);

/**
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
