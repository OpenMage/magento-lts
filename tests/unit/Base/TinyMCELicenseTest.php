<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Base;

use Composer\InstalledVersions;
use PHPUnit\Framework\TestCase;

final class TinyMCELicenseTest extends TestCase
{
    public const TINY_MCE_NAMESPACE = 'tinymce/tinymce';

    public const ERROR_MESSAGE = "License file doesn't exist.";

    public const SKIP_MESSAGE = 'TinyMCE is not installed.';

    /**
     * @group Base
     * @group TinyMCE
     */
    public function testRootLicenseFilesExists(): void
    {
        if (InstalledVersions::isInstalled(self::TINY_MCE_NAMESPACE)) {
            $rootDir = dirname(__DIR__, 3);
            $filename = $rootDir . DIRECTORY_SEPARATOR . 'LICENSE_TINYMCE.txt';
            self::assertFileExists($filename, self::ERROR_MESSAGE);
        } else {
            self::markTestSkipped(self::SKIP_MESSAGE);
        }
    }

    /**
     * @group Base
     * @group TinyMCE
     */
    public function testVendorLicenseFilesExists(): void
    {
        if (InstalledVersions::isInstalled(self::TINY_MCE_NAMESPACE)) {
            $vendorPath = InstalledVersions::getInstallPath(self::TINY_MCE_NAMESPACE);
            $filename = $vendorPath . DIRECTORY_SEPARATOR . 'LICENSE_TINYMCE_OPENMAGE.txt';
            self::assertFileExists($filename, self::ERROR_MESSAGE);
        } else {
            self::markTestSkipped(self::SKIP_MESSAGE);
        }
    }
}
