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

use Exception;
use Generator;
use Mage;
use Mage_Core_Helper_UnserializeArray;
use PHPUnit\Framework\TestCase;
use Varien_Object;

class UnserializeArrayTest extends TestCase
{
    public Mage_Core_Helper_UnserializeArray $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::helper('core/unserializeArray');
    }

    /**
     * @dataProvider provideUnserialize
     * @group Mage_Core
     * @group Mage_Core_Helper
     */
    public function testUnserialize($expectedTesult, $string): void
    {
        try {
            $this->assertSame($expectedTesult, $this->subject->unserialize($string));
        } catch (Exception $exception) {
            $this->assertSame($expectedTesult, $exception->getMessage());
        }
    }

    public function provideUnserialize(): Generator
    {
        yield 'null' => [
            'Error unserializing data.',
            null,
        ];
        yield 'empty string' => [
            'Error unserializing data.',
            '',
        ];
        yield 'random string' => [
            'unserialize(): Error at offset 0 of 3 bytes',
            'abc',
        ];
        yield 'valid' => [
            ['key' => 'value'],
            'a:1:{s:3:"key";s:5:"value";}',
        ];
    }
}
