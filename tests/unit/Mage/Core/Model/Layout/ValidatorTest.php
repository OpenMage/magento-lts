<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Layout;

use Override;
use Generator;
use Mage;
use Mage_Core_Model_Layout_Validator as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\UrlTrait;

final class ValidatorTest extends OpenMageTest
{
    use UrlTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/layout_validator');
    }

    /**
     * @dataProvider isValidDataProvider
     * @group Model
     * @param array<string, string> $expectedErrors
     */
    public function testIsValid(bool $expected, string $value, array $expectedErrors): void
    {
        // fresh instance: validator messages accumulate across isValid() calls
        $subject = Mage::getModel('core/layout_validator');
        self::assertSame($expected, $subject->isValid($value));

        if (!$expected) {
            $messages = $subject->getMessages();
            self::assertIsArray($messages);
            self::assertSame($expectedErrors, $messages);
        }
    }

    public static function isValidDataProvider(): Generator
    {
        yield 'valid string' => [
            true,
            'default',
            [],
        ];

        yield 'invalid string' => [
            false,
            '<invalid-node>',
            [
                'invalidXml' => 'XML data is invalid.',
            ],
        ];

        yield 'plain action without helper' => [
            true,
            '<reference name="content"><action method="setText"><text>x</text></action></reference>',
            [],
        ];

        yield 'helper attribute' => [
            false,
            '<reference name="content"><action method="setText"><text helper="core/string/truncate"/></action></reference>',
            [
                'protectedAttrHelperInActionVar' => 'Helper attributes should not be used in custom layout updates.',
            ],
        ];

        yield 'helper attribute under default-namespaced action (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<reference name="content"><action xmlns="urn:x" method="setText"><text helper="core/string/truncate"/></action></reference>',
            [
                'invalidXml' => 'XML data is invalid.',
            ],
        ];

        yield 'default namespace on outer element (GHSA-v2qw-3rm9-qx3p)' => [
            false,
            '<reference xmlns="urn:x" name="content"><action method="setText"><text>x</text></action></reference>',
            [
                'invalidXml' => 'XML data is invalid.',
            ],
        ];

        yield 'prefixed namespace element' => [
            false,
            '<reference xmlns:n="urn:x" name="content"><n:action method="setText"/></reference>',
            [
                'invalidXml' => 'XML data is invalid.',
            ],
        ];

        yield 'namespaced attribute' => [
            false,
            '<reference xmlns:n="urn:x" name="content" n:foo="bar"/>',
            [
                'invalidXml' => 'XML data is invalid.',
            ],
        ];

        yield 'template path traversal' => [
            false,
            '<reference name="content"><block type="core/template" name="t" template="../x.phtml"/></reference>',
            [
                'invalidTemplatePath' => 'Invalid template path used in layout update.',
            ],
        ];
    }
}
