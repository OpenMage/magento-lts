<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_String;
use PHPUnit\Framework\TestCase;

class StringTest extends TestCase
{
    public const TEST_STRING = '1234567890';

    /**
     * @var Mage_Core_Helper_String
     */
    private Mage_Core_Helper_String $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/string');
    }

    public function testSubstr(): void
    {
        $resultString = $this->subject->substr(self::TEST_STRING, 2, 2);
        self::assertEquals('34', $resultString);
    }

    public function testTruncate(): void
    {
        $resultString = $this->subject->truncate(self::TEST_STRING, 5, '...');
        self::assertEquals('12...', $resultString);
    }

    public function testStrlen(): void
    {
        self::assertEquals(10, $this->subject->strlen(self::TEST_STRING));
    }
}
