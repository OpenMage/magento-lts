<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Layout;

use Generator;
use Mage;
use Mage_Core_Model_Layout_Validator as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Base\UrlTrait;

final class ValidatorTest extends OpenMageTest
{
    use UrlTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/layout_validator');
    }

    /**
     * @dataProvider isValidDataProvider
     * @group Model
     */
    public function testIsValid(bool $expected, string $value, array $expectedErrors): void
    {
        self::assertSame($expected, self::$subject->isValid($value));

        if (!$expected) {
            $messages = self::$subject->getMessages();
            self::assertIsArray($messages);
            self::assertSame($expectedErrors, $messages);
        }
    }

    public function isValidDataProvider(): Generator
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
    }
}
