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
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Resource\Eav;

use Mage;
use Mage_Catalog_Model_Resource_Eav_Attribute as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\CoreTrait;
use OpenMage\Tests\Unit\OpenMageTest;

class AttributeTest extends OpenMageTest
{
    use CoreTrait;

    private static Subject $subject;

    public function setUp(): void
    {
        self::$subject = Mage::getModel('catalog/resource_eav_attribute');
    }

    /**
     * @dataProvider provideGetStoreId
     * @group Model
     */
    public function testGetStoreId(?int $expectedResult, int|string|null $withStoreId): void
    {
        if ($withStoreId) {
            self::$subject->setStoreId($withStoreId);
        }
        static::assertSame($expectedResult, self::$subject->getStoreId());
    }
}
