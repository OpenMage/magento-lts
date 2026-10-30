<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Model\Config;

use Mage_Adminhtml_Model_Config_Data as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Model\Config\DataTrait;
use ReflectionMethod;

final class DataTest extends OpenMageTest
{
    use DataTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = new Subject();
    }

    /**
     * Group and field keys are concatenated into an XPath expression for the
     * dynamic_group fallback lookup (GHSA-wrp2-grgr-7gr9). Only a plain
     * identifier may reach that expression, or a key such as
     * "x|//admin/groups/url/fields/use_custom" selects a node outside the
     * requested section.
     *
     * @dataProvider provideIdentifiers
     */
    public function testIsPlainIdentifier(bool $expected, mixed $value): void
    {
        $method = new ReflectionMethod(self::$subject, '_isPlainIdentifier');
        self::assertSame($expected, $method->invoke(self::$subject, $value));
    }

    public static function provideIdentifiers(): array
    {
        return [
            'simple field name' => [true, 'use_custom'],
            'name with underscore' => [true, 'base_url'],
            'numeric string' => [true, '123'],
            'xpath union injection' => [false, 'x|//admin/groups/url/fields/use_custom'],
            'absolute path injection' => [false, '//admin/groups/url/fields/use_custom'],
            'path traversal' => [false, '../../web/cookie_domain'],
            'slash separated path' => [false, 'url/fields/use_custom'],
            'attribute predicate injection' => [false, 'use_custom[@type="group"]'],
            'empty string' => [false, ''],
            'array value' => [false, ['use_custom']],
            'null value' => [false, null],
        ];
    }
}
