<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Security;

use Mage_Core_Model_Security_HtmlEscapedString;
use PHPUnit\Framework\TestCase;

class HtmlEscapedStringTest extends TestCase
{
    public const TEST_STRING = 'This is a bold <b>string</b>';

    /**
     * @var Mage_Core_Model_Security_HtmlEscapedString
     */
    public Mage_Core_Model_Security_HtmlEscapedString $subject;

    /**
     * @dataProvider provideHtmlEscapedStringAsStringData
     * @param string $expectedResult
     * @param string $string
     * @param array<int, string> $allowedTags
     * @return void
     */
    public function testToSting(string $expectedResult, string $string, ?array $allowedTags): void
    {
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $this->subject = new Mage_Core_Model_Security_HtmlEscapedString($string, $allowedTags);
        $this->assertSame($expectedResult, $this->subject->__toString());
    }

    /**
     * @dataProvider provideHtmlEscapedStringGetUnescapedValueData
     * @param string $expectedResult
     * @param string $string
     * @param array<int, string> $allowedTags
     * @return void
     */
    public function testGetUnescapedValue(string $expectedResult, string $string, ?array $allowedTags): void
    {
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $this->subject = new Mage_Core_Model_Security_HtmlEscapedString($string, $allowedTags);
        $this->assertSame($expectedResult, $this->subject->getUnescapedValue());
    }

    /**
     * @return array<string, array<int, array<int, string>|string|null>>
     */
    public function provideHtmlEscapedStringAsStringData(): array
    {
        return [
            'tags_null' => [
                'This is a bold &lt;b&gt;string&lt;/b&gt;',
                self::TEST_STRING,
                null
            ],
            'tags_array' => [
                self::TEST_STRING,
                self::TEST_STRING,
                ['b']
            ],
        ];
    }

    /**
     * @return array<string, array<int, array<int, string>|string|null>>
     */
    public function provideHtmlEscapedStringGetUnescapedValueData(): array
    {
        return [
            'tags_null' => [
                self::TEST_STRING,
                self::TEST_STRING,
                null
            ],
            'tags_array' => [
                self::TEST_STRING,
                self::TEST_STRING,
                ['some-invalid-value']
            ],
        ];
    }
}
