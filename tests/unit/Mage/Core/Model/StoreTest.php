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
use PHPUnit\Framework\TestCase;

class StoreTest extends TestCase
{
    use CoreTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('core/store');
    }

    /**
     * @covers Mage_Core_Model_Store::getId()
     * @dataProvider provideGetStoreId
     * @param string|int|null $withStore
     * @group Mage_Core
     * @group Mage_Core_Model
     */
    public function testGetId(?int $expectedResult, $withStore): void
    {
        if ($withStore) {
            $this->subject->setData('store_id', $withStore);
        }
        $this->assertSame($expectedResult, $this->subject->getId());
    }
}
