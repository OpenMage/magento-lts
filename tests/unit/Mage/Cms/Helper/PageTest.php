<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cms\Helper;

use Generator;
use Mage_Cms_Helper_Page as Subject;
use ReflectionMethod;
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
     * @param non-empty-string $expectedResult
     * @covers Mage_Cms_Helper_Page::getScopeInfoFromConfigScope()
     * @dataProvider provideGetScopeInfoFromConfigScope
     * @group Helper
     */
    public function testGetScopeInfoFromConfigScope(string $expectedResult, string $scope, string $scopeId): void
    {
        self::assertStringStartsWith($expectedResult, Subject::getScopeInfoFromConfigScope($scope, $scopeId));
    }

    /**
     * Stored CMS page layout updates are validated again when the page is rendered
     *
     * @dataProvider provideGetValidLayoutUpdate
     * @group Helper
     */
    public function testGetValidLayoutUpdate(string $expectedResult, string $layoutUpdate): void
    {
        $method = new ReflectionMethod(Subject::class, '_getValidLayoutUpdate');
        self::assertSame($expectedResult, $method->invoke(new Subject(), $layoutUpdate));
    }

    public static function provideGetValidLayoutUpdate(): Generator
    {
        yield 'empty' => ['', ''];

        $valid = '<reference name="content"><block type="core/text" name="t"><action method="setText"><t>x</t></action></block></reference>';
        yield 'valid update is applied' => [$valid, $valid];

        yield 'admin grid is dropped' => [
            '',
            '<reference name="content"><block type="adminhtml/cms_page_grid" name="g"><action method="getCsv"/></block></reference>',
        ];

        yield 'json action argument is dropped' => [
            '',
            '<reference name="content"><action method="setData" json="v"><k>x</k><v>"y"</v></action></reference>',
        ];

        yield 'helper argument is dropped' => [
            '',
            '<reference name="content"><action method="setText"><t helper="core/string/truncate"/></action></reference>',
        ];

        yield 'invalid XML is dropped' => ['', '<reference name="content">'];
    }
}
