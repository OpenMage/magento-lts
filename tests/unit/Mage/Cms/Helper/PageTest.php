<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Helper;

use Mage_Cms_Helper_Page as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cms\CmsTrait;
use OpenMage\Tests\Unit\OpenMageTest;

final class PageTest extends OpenMageTest
{
    use CmsTrait;

    /**
     * @covers Mage_Cms_Helper_Page::getUsedInStoreConfigPaths()
     * @dataProvider provideGetUsedInStoreConfigPaths
     * @group Helper
     */
    public function testGetUsedInStoreConfigPaths(array $expectedResult, ?array $path): void
    {
        self::assertSame($expectedResult, Subject::getUsedInStoreConfigPaths($path));
    }

    /**
     * @covers Mage_Cms_Helper_Page::getConfigLabelFromConfigPath()
     * @dataProvider provideGetConfigLabelFromConfigPath
     * @group Helper
     */
    public function testGetConfigLabelFromConfigPath(string $expectedResult, string $paths): void
    {
        self::assertSame($expectedResult, Subject::getConfigLabelFromConfigPath($paths));
    }

    /**
     * @covers Mage_Cms_Helper_Page::getScopeInfoFromConfigScope()
     * @dataProvider provideGetScopeInfoFromConfigScope
     * @group Helper
     */
    public function testGetScopeInfoFromConfigScope(string $expectedResult, string $scope, string $scopeId): void
    {
        self::assertStringStartsWith($expectedResult, Subject::getScopeInfoFromConfigScope($scope, $scopeId));
    }
}
