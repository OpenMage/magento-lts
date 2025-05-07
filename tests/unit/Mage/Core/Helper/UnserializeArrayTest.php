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
use Mage;
use Mage_Core_Helper_UnserializeArray as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper\UnserializeArray as UnserializeArrayDataProvider;

class UnserializeArrayTest extends OpenMageTest
{
    use UnserializeArrayDataProvider;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('core/unserializeArray');
    }

    /**
     * @dataProvider provideUnserialize
     * @group Helper
     */
    public function testUnserialize(array|string $expectedTesult, ?string $string): void
    {
        try {
            static::assertSame($expectedTesult, self::$subject->unserialize($string));
        } catch (Exception $exception) {
            static::assertSame($expectedTesult, $exception->getMessage());
        }
    }
}
