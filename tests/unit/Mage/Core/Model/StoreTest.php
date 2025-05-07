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

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Store as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\CoreTrait;
use OpenMage\Tests\Unit\OpenMageTest;

class StoreTest extends OpenMageTest
{
    use CoreTrait;

    private static Subject $subject;

    public function setUp(): void
    {
        self::$subject = Mage::getModel('core/store');
    }

    /**
     * @covers Mage_Core_Model_Store::getId()
     * @dataProvider provideGetStoreId
     * @param string|int|null $withStore
     * @group Model
     */
    public function testGetId(?int $expectedResult, $withStore): void
    {
        if ($withStore) {
            self::$subject->setData('store_id', $withStore);
        }
        static::assertSame($expectedResult, self::$subject->getId());
    }
}
