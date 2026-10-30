<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Base;

use Mage;
use PHPUnit\Framework\TestCase;

final class MageVersionTest extends TestCase
{
    /**
     * getVersion() must not call the deprecated getVersionInfo()
     */
    public function testGetVersionWithoutDeprecationNotice(): void
    {
        $deprecations = [];
        set_error_handler(static function (int $errno, string $errstr) use (&$deprecations): bool {
            $deprecations[] = $errstr;
            return true;
        }, E_USER_DEPRECATED | E_DEPRECATED);

        try {
            $version = Mage::getVersion();
        } finally {
            restore_error_handler();
        }

        self::assertSame('1.9.4.5', $version);
        self::assertSame([], $deprecations);
    }
}
