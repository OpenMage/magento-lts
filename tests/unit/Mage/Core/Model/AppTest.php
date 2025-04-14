<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Generator;
use Mage;
use Mage_Core_Exception;
use Mage_Core_Model_App as Subject;
use Mage_Core_Model_Store;
use Mage_Core_Model_Store_Exception;
use Mage_Core_Model_Store_Group;
use Mage_Core_Model_Website;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\CoreTrait;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    use CoreTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        self::$subject = Mage::app();
    }

    /**
     * @dataProvider provideGetStoreConfig
     * @dataProvider provideGetStore
     * @group Mage_Core
     * @group Mage_Core_Model
     *
     * @param bool|int|Mage_Core_Model_Store|null|string $id
     */
    public function testGetStore($id): void
    {
        try {
            static::assertInstanceOf(Mage_Core_Model_Store::class, self::$subject->getStore($id));
        } catch (Mage_Core_Model_Store_Exception $e) {
            static::assertNotEmpty($e->getMessage());
            static::assertSame('Invalid store code requested.', $e->getMessage());
        }
    }

    public function provideGetStore(): Generator
    {
        yield 'Mage_Core_Model_Store' => [
            new Mage_Core_Model_Store(),
        ];
    }

    /**
     * @dataProvider provideGetStoreConfig
     * @dataProvider provideGetWebsite
     * @group Mage_Core
     * @group Mage_Core_Model
     *
     * @param int|Mage_Core_Model_Website|null|string|true $id
     */
    public function testGetWebsite($id): void
    {
        try {
            static::assertInstanceOf(Mage_Core_Model_Website::class, self::$subject->getWebsite($id));
        } catch (Mage_Core_Exception $e) {
            static::assertNotEmpty($e->getMessage());
            static::assertSame('Invalid website id requested.', $e->getMessage());
        }
    }

    public function provideGetWebsite(): Generator
    {
        yield 'Mage_Core_Model_Website' => [
            new Mage_Core_Model_Website(),
        ];
    }

    /**
     * @dataProvider provideGetStoreConfig
     * @dataProvider provideGetGroup
     * @group Mage_Core
     * @group Mage_Core_Model
     *
     * @param int|Mage_Core_Model_Store_Group|null|string $id
     */
    public function testGetGroup($id): void
    {
        try {
            static::assertInstanceOf(Mage_Core_Model_Store_Group::class, self::$subject->getGroup($id));
        } catch (Mage_Core_Exception $e) {
            static::assertNotEmpty($e->getMessage());
            static::assertSame('Invalid store group id requested.', $e->getMessage());
        }
    }

    public function provideGetGroup(): Generator
    {
        yield 'Mage_Core_Model_Store_Group' => [
            new Mage_Core_Model_Store_Group(),
        ];
    }
}
