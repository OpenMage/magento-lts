<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_Array;
use Mage_Core_Helper_String as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class StringTest extends OpenMageTest
{
    public const TEST_STRING        = '1234567890';

    public const TEST_STRING_JSON   = '{"name":"John", "age":30, "car":null}';

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('core/string');
    }

    /**
     * @group Helper
     */
    public function testTruncate(): void
    {
        static::assertSame('', self::$subject->truncate(null));
        static::assertSame('', self::$subject->truncate(self::TEST_STRING, 0));

        static::assertSame('', self::$subject->truncate(self::TEST_STRING, 3));

        $remainder = '';
        static::assertSame('12...', self::$subject->truncate(self::TEST_STRING, 5, '...', $remainder, false));

        $resultString = self::$subject->truncate(self::TEST_STRING, 5, '...');
        static::assertSame('12...', $resultString);
    }

    /**
     * @group Helper
     */
    public function testSubstr(): void
    {
        $resultString = self::$subject->substr(self::TEST_STRING, 2, 2);
        static::assertSame('34', $resultString);
    }

    /**
     * @group Helper
     */
    public function testSplitInjection(): void
    {
        $resultString = self::$subject->splitInjection(self::TEST_STRING, 1, '-', ' ');
        #$this->assertSame('1-2-3-4-5-6-7-8-9-0-', $resultString);
        static::assertIsString($resultString);
    }

    /**
     * @group Helper
     */
    public function testStrlen(): void
    {
        static::assertSame(10, self::$subject->strlen(self::TEST_STRING));
    }

    /**
     * @group Helper
     */
    public function testStrSplit(): void
    {
        static::assertIsArray(self::$subject->str_split(''));
        static::assertIsArray(self::$subject->str_split(self::TEST_STRING));
        static::assertIsArray(self::$subject->str_split(self::TEST_STRING, 3));
        static::assertIsArray(self::$subject->str_split(self::TEST_STRING, 3, true, true));
    }

    /**
     * @group Helper
     */
    public function testSplitWords(): void
    {
        static::assertIsArray(self::$subject->splitWords(null));
        static::assertIsArray(self::$subject->splitWords(''));
        static::assertIsArray(self::$subject->splitWords(self::TEST_STRING));
        static::assertIsArray(self::$subject->splitWords(self::TEST_STRING, true));
        static::assertIsArray(self::$subject->splitWords(self::TEST_STRING, true, 1));
    }

    /**
     * @group Helper
     */
    public function testParseQueryStr(): void
    {
        static::assertIsArray(self::$subject->parseQueryStr(self::TEST_STRING));
    }

    /**
     * @group Helper
     */
    public function testGetArrayHelper(): void
    {
        static::assertInstanceOf(Mage_Core_Helper_Array::class, self::$subject->getArrayHelper());
    }

    /**
     * @group Helper
     */
    public function testUnserialize(): void
    {
        static::assertNull(self::$subject->unserialize(null));
    }

    /**
     * @group Helper
     */
    public function testValidateSerializedObject(): void
    {
        static::assertIsBool(self::$subject->validateSerializedObject(self::TEST_STRING));
        static::assertIsBool(self::$subject->validateSerializedObject(self::TEST_STRING_JSON));
    }
}
