<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Design;

use Mage;
use Mage_Core_Model_Config;
use Mage_Core_Model_Design_Fallback as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\Design\FallbackTrait;

final class FallbackTest extends OpenMageTest
{
    use FallbackTrait;

    private const ADMIN_THEME_PATH = 'stores/admin/design/theme/default';

    private string $adminTheme;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminTheme = (string) $this->getConfig()->getNode(self::ADMIN_THEME_PATH);
    }

    protected function tearDown(): void
    {
        $this->getConfig()->setNode(self::ADMIN_THEME_PATH, $this->adminTheme);
        parent::tearDown();
    }

    /**
     * A configured admin theme that does not exist in the package falls back to the package's
     * default theme and its parents, so theme.xml layout updates of openmage/default still apply
     *
     * @dataProvider provideGetFallbackSchemeData
     * @param array<int, array<string, string>> $expected
     * @group Model
     */
    public function testGetFallbackScheme(array $expected, string $configuredTheme, string $package, string $theme): void
    {
        $this->getConfig()->setNode(self::ADMIN_THEME_PATH, $configuredTheme);

        self::assertSame($expected, (new Subject())->getFallbackScheme('adminhtml', $package, $theme));
    }

    private function getConfig(): Mage_Core_Model_Config
    {
        $config = Mage::getConfig();
        self::assertInstanceOf(Mage_Core_Model_Config::class, $config);

        return $config;
    }
}
