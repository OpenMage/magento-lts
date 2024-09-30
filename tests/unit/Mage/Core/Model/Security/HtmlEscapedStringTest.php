<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Security;

use Mage_Core_Model_Security_HtmlEscapedString;
use PHPUnit\Framework\TestCase;

class HtmlEscapedStringTest extends TestCase
{
    public const TEST_STRING = 'This is a bold <b>string</b>';

    public Mage_Core_Model_Security_HtmlEscapedString $subject;

    /**
     * @dataProvider provideHtmlEscapedStringAsStringData
     * @param array<int, string> $allowedTags
     *
     * @group Mage_Core
     */
    public function testToSting(string $expectedResult, string $string, ?array $allowedTags): void
    {
        // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
        $this->subject = new Mage_Core_Model_Security_HtmlEscapedString($string, $allowedTags);
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
