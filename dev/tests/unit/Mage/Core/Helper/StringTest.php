<?php
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage_Core_Helper_String;
use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
{
    const TEST_STRING_1 = 'Test 12345 a lot more Text';

    public function testSubstr()
    {
        $subject = new Mage_Core_Helper_String();
        $resultString = $subject->substr(
            self::TEST_STRING_1,
            5,
            5
        );
        $this->assertEquals(
            '12345',
            $resultString
        );
    }

    public function testTruncate()
    {
        $subject = new Mage_Core_Helper_String();
        $resultString = $subject->truncate(
            self::TEST_STRING_1,
            13,
            '###'
        );
        $this->assertEquals(
            'Test 12345###',
            $resultString
        );

    }

    public function testStrlen()
    {
        $subject = new Mage_Core_Helper_String();
        $this->assertEquals(
            26,
            $subject->strlen(self::TEST_STRING_1)
        );
    }
}
