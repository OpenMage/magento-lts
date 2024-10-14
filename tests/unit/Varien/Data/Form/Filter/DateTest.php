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

namespace OpenMage\Tests\Unit\Varien\Data\Form\Filter;

use PHPUnit\Framework\TestCase;
use Throwable;
use Varien_Data_Form_Filter_Date;

class DateTest extends TestCase
{
    public Varien_Data_Form_Filter_Date $subject;

    public function setUp(): void
    {
        $this->subject = new Varien_Data_Form_Filter_Date(null, 'en_US');
    }

    /**
     * @group Varien_Data
     */
    public function testInputFilter(): void
    {
        $this->assertEquals('', $this->subject->inputFilter(''));
        $this->assertEquals(null, $this->subject->inputFilter(null));
        $this->assertEquals('1990-05-18', $this->subject->inputFilter('1990-05-18'));
        $this->assertEquals('0090-05-18', $this->subject->inputFilter('90-05-18'));
        $this->assertEquals('1990-05-08', $this->subject->inputFilter('1990-5-8'));
        $this->assertEquals('1970-01-01', $this->subject->inputFilter('1970-01-01'));

        try {
            $this->subject->inputFilter('1990-18-18');
        } catch (Throwable $e) {
            // PHP7: bcsub(): bcmath function argument is not well-formed
            // PHP8: bcsub(): Argument #1 ($num1) is not well-formed
            $this->assertStringStartsWith('bcsub():', $e->getMessage());
        }
    }
}
