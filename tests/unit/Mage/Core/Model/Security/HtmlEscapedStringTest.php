<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Security;

use Mage_Core_Model_Security_HtmlEscapedString as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\SecurityTrait;

final class HtmlEscapedStringTest extends OpenMageTest
{
    use SecurityTrait;

    private static Subject $subject;

    /**
     * @dataProvider provideHtmlEscapedStringAsStringData
     * @param array<int, string> $allowedTags
     *
     * @group Model
     */
    public function testToSting(string $expectedResult, string $string, ?array $allowedTags): void
    {
        self::$subject = new Subject($string, $allowedTags);
        static::assertSame($expectedResult, self::$subject->__toString());
    }

    /**
     * @dataProvider provideHtmlEscapedStringGetUnescapedValueData
     * @param array<int, string> $allowedTags
     *
     * @group Model
     */
    public function testGetUnescapedValue(string $expectedResult, string $string, ?array $allowedTags): void
    {
        self::$subject = new Subject($string, $allowedTags);
        static::assertSame($expectedResult, self::$subject->getUnescapedValue());
    }
}
