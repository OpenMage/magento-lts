<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Store as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\CoreTrait;
use OpenMage\Tests\Unit\OpenMageTest;

final class StoreTest extends OpenMageTest
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
