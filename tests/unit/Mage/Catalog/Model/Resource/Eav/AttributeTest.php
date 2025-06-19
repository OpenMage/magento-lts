<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
