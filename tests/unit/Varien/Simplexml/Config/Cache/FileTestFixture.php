<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Varien\Simplexml\Config\Cache;

/**
 * Records whether it was instantiated while a cache stat file was unserialized
 */
final class FileTestFixture
{
    public static bool $awakened = false;

    public function __wakeup(): void
    {
        self::$awakened = true;
    }
}
