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
use Mage_Core_Helper_Array as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper\ArrayTrait;

final class ArrayTest extends OpenMageTest
{
    use ArrayTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('core/array');
    }

    /**
     * @covers Mage_Core_Helper_Data::getMerchantCountryCode()
     * @dataProvider provideMergeRecursiveWithoutOverwriteNumKeysData
     * @group Helper
     */
    public function testMergeRecursiveWithoutOverwriteNumKeys(array $expectedResult, array $baseArray, array $mergeArray): void
    {
        self::assertSame($expectedResult, self::$subject->mergeRecursiveWithoutOverwriteNumKeys($baseArray, $mergeArray));
    }
}
