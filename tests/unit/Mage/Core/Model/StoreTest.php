<?php

/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @covers Mage_Core_Model_Store::getId()
 * @dataProvider provideGetStoreId
 * @param string|int|null $withStore
 * @group Mage_Core
 * @group Mage_Core_Model
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


    public function testGetId(?int $expectedResult, $withStore): void
    {
        if ($withStore) {
            $this->subject->setData('store_id', $withStore);
        }
        $this->assertSame($expectedResult, $this->subject->getId());
    }
}
