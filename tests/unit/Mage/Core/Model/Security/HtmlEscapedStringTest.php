<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Security;

use Generator;
use Mage_Core_Model_Security_HtmlEscapedString as Subject;
use PHPUnit\Framework\TestCase;

class HtmlEscapedStringTest extends TestCase
{
    public const TEST_STRING = 'This is a bold <b>string</b>';

    public Subject $subject;

    /**
     * @dataProvider provideHtmlEscapedStringAsStringData
     * @param array<int, string> $allowedTags
     *
     * @group Mage_Core
     */
    public function testToSting(string $expectedResult, string $string, ?array $allowedTags): void
    {
        $this->subject = new Subject($string, $allowedTags);
        $this->assertSame($expectedResult, $this->subject->__toString());
    }

    /**
     * @dataProvider provideHtmlEscapedStringGetUnescapedValueData
     * @param array<int, string> $allowedTags
     *
     * @group Mage_Core
     */
    public function testGetUnescapedValue(string $expectedResult, string $string, ?array $allowedTags): void
    {
        $this->subject = new Subject($string, $allowedTags);
        $this->assertSame($expectedResult, $this->subject->getUnescapedValue());
    }

    public function provideHtmlEscapedStringAsStringData(): Generator
    {
        yield 'tags null' => [
            'This is a bold &lt;b&gt;string&lt;/b&gt;',
            self::TEST_STRING,
            null,
        ];
        yield 'tags array' => [
            self::TEST_STRING,
            self::TEST_STRING,
            ['b'],
        ];
    }

    public function provideHtmlEscapedStringGetUnescapedValueData(): Generator
    {
        yield 'tags null' => [
            self::TEST_STRING,
            self::TEST_STRING,
            null,
        ];
        yield 'tags array' => [
            self::TEST_STRING,
            self::TEST_STRING,
            ['some-invalid-value'],
        ];
    }
}
