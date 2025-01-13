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

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_Array;
use Mage_Core_Helper_String;
use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
{
    public const TEST_STRING        = '1234567890';

    public const TEST_STRING_JSON   = '{"name":"John", "age":30, "car":null}';

    public Mage_Core_Helper_String $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/string');
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testTruncate(): void
    {
        static::assertSame('', $this->subject->truncate(null));
        static::assertSame('', $this->subject->truncate(self::TEST_STRING, 0));

        static::assertSame('', $this->subject->truncate(self::TEST_STRING, 3));

        $remainder = '';
        static::assertSame('12...', $this->subject->truncate(self::TEST_STRING, 5, '...', $remainder, false));

        $resultString = $this->subject->truncate(self::TEST_STRING, 5, '...');
        static::assertSame('12...', $resultString);
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testSubstr(): void
    {
        $resultString = $this->subject->substr(self::TEST_STRING, 2, 2);
        static::assertSame('34', $resultString);
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testSplitInjection(): void
    {
        $resultString = $this->subject->splitInjection(self::TEST_STRING, 1, '-', ' ');
        #$this->assertSame('1-2-3-4-5-6-7-8-9-0-', $resultString);
        static::assertIsString($resultString);
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testStrlen(): void
    {
        static::assertSame(10, $this->subject->strlen(self::TEST_STRING));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testStrSplit(): void
    {
        static::assertIsArray($this->subject->str_split(''));
        static::assertIsArray($this->subject->str_split(self::TEST_STRING));
        static::assertIsArray($this->subject->str_split(self::TEST_STRING, 3));
        static::assertIsArray($this->subject->str_split(self::TEST_STRING, 3, true, true));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testSplitWords(): void
    {
        static::assertIsArray($this->subject->splitWords(null));
        static::assertIsArray($this->subject->splitWords(''));
        static::assertIsArray($this->subject->splitWords(self::TEST_STRING));
        static::assertIsArray($this->subject->splitWords(self::TEST_STRING, true));
        static::assertIsArray($this->subject->splitWords(self::TEST_STRING, true, 1));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testParseQueryStr(): void
    {
        static::assertIsArray($this->subject->parseQueryStr(self::TEST_STRING));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testGetArrayHelper(): void
    {
        static::assertInstanceOf(Mage_Core_Helper_Array::class, $this->subject->getArrayHelper());
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testUnserialize(): void
    {
        static::assertNull($this->subject->unserialize(null));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testValidateSerializedObject(): void
    {
        static::assertIsBool($this->subject->validateSerializedObject(self::TEST_STRING));
        static::assertIsBool($this->subject->validateSerializedObject(self::TEST_STRING_JSON));
    }
}
