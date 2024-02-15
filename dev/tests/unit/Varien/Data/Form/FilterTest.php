<?php
declare(strict_types=1);

namespace Varien\Data\Form;

use Mage_Core_Helper_String;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{

    public function testDateFilter()
    {
        $subject = new \Varien_Data_Form_Filter_Date(null, 'en_US');

        $this->assertEquals(
            '',
            $subject->inputFilter('')
        );
        $this->assertEquals(
            null,
            $subject->inputFilter(null)
        );
        $this->assertEquals(
            '1990-05-18',
            $subject->inputFilter('1990-05-18')
        );
        $this->assertEquals(
            '0090-05-18',
            $subject->inputFilter('90-05-18')
        );
        $this->assertEquals(
            '1990-05-08',
            $subject->inputFilter('1990-5-8')
        );
        $this->assertEquals(
            '1970-01-01',
            $subject->inputFilter('1970-01-01')
        );
   
        try {
            $subject->inputFilter('1990-18-18');
            $this->fail('expected a ValueError'); // ValueError: bcsub(): Argument #1 ($num1) is not well-formed
        } catch (\ValueError $e) {
            $this->assertStringContainsString('bcsub', $e->getMessage());
        }
    }

    public function testDateTimeFilter()
    {
        $subject = new \Varien_Data_Form_Filter_Datetime(null, 'en_US');

        $this->assertEquals(
            '',
            $subject->inputFilter('')
        );
        $this->assertEquals(
            null,
            $subject->inputFilter(null)
        );
        $this->assertEquals(
            '1990-05-18 00:00:00',
            $subject->inputFilter('1990-05-18')
        );
        $this->assertEquals(
            '0090-05-18 00:00:00',
            $subject->inputFilter('90-05-18')
        );
        $this->assertEquals(
            '1990-05-08 00:00:00',
            $subject->inputFilter('1990-5-8')
        );

        try {
            $subject->inputFilter('1990-18-18');
            $this->fail('expected a ValueError'); // ValueError: bcsub(): Argument #1 ($num1) is not well-formed
        } catch (\ValueError $e) {
            $this->assertStringContainsString('bcsub', $e->getMessage());
        }
    }
}
