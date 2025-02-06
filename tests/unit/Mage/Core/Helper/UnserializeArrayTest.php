<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Exception;
use Generator;
use Mage;
use Mage_Core_Helper_UnserializeArray as Subject;
use PHPUnit\Framework\TestCase;

class UnserializeArrayTest extends TestCase
{
    public Subject $subject;

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
        $errorMessage = 'Error unserializing data.';

        yield 'null' => [
            $errorMessage,
            null,
        ];
        yield 'empty string' => [
            $errorMessage,
            '',
        ];
        yield 'random string' => [
            $errorMessage,
            'abc',
        ];
        yield 'valid' => [
            ['key' => 'value'],
            'a:1:{s:3:"key";s:5:"value";}',
        ];
    }
}
