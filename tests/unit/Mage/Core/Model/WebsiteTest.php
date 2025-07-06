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
use Mage_Core_Model_Resource_Store_Collection;
use Mage_Core_Model_Resource_Store_Group_Collection;
use Mage_Core_Model_Website as Subject;
use Mage_Directory_Model_Currency;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Db_Select;

final class WebsiteTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/website');
    }

    /**
     * @group Model
     */
    public function testLoad(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->load(1));
        static::assertInstanceOf(Subject::class, self::$subject->load('default'));
    }

    /**
     * @group Model
     */
    public function testLoadConfig(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->loadConfig('1'));
        static::assertInstanceOf(Subject::class, self::$subject->loadConfig('default'));
    }

    /**
     * @group Model
     */
    public function testGetStoreCollection(): void
    {
        static::assertInstanceOf(Mage_Core_Model_Resource_Store_Collection::class, self::$subject->getStoreCollection());
    }

    /**
     * @group Model
     */
    public function testGetGroupCollection(): void
    {
        static::assertInstanceOf(Mage_Core_Model_Resource_Store_Group_Collection::class, self::$subject->getGroupCollection());
    }

    /**
     * @group Model
     */
    public function testGetStores(): void
    {
        static::assertIsArray(self::$subject->getStores());
    }

    /**
     * @group Model
     */
    public function testGetStoreIds(): void
    {
        static::assertIsArray(self::$subject->getStoreIds());
    }

    /**
     * @group Model
     */
    public function testGetStoreCodes(): void
    {
        static::assertIsArray(self::$subject->getStoreCodes());
    }

    /**
     * @group Model
     */
    public function testGetStoresCount(): void
    {
        static::assertIsInt(self::$subject->getStoresCount());
    }

    /**
     * @group Model
     */
    public function testGetGroups(): void
    {
        static::assertIsArray(self::$subject->getGroups());
    }

    /**
     * @group Model
     */
    public function testGetGroupIds(): void
    {
        static::assertIsArray(self::$subject->getGroupIds());
    }

    /**
     * @group Model
     */
    public function testGetGroupsCount(): void
    {
        static::assertIsInt(self::$subject->getGroupsCount());
    }

    /**
     * @group Model
     */
    public function testGetBaseCurrency(): void
    {
        static::assertIsObject(self::$subject->getBaseCurrency());
        static::assertInstanceOf(Mage_Directory_Model_Currency::class, self::$subject->getBaseCurrency());
    }

    //    /**
    //     * @group Model
    //     */
    //    public function testGetDefaultStore(): void
    //    {
    //        $this->assertIsObject(self::$subject->getDefaultStore());
    //        $this->assertInstanceOf(Mage_Core_Model_Store::class, self::$subject->getDefaultStore());
    //    }

    /**
     * @group Model
     */
    public function testGetDefaultStoresSelect(): void
    {
        static::assertIsObject(self::$subject->getDefaultStoresSelect());
        static::assertInstanceOf(Varien_Db_Select::class, self::$subject->getDefaultStoresSelect(true));
    }

    /**
     * @group Model
     */
    public function testIsReadOnly(): void
    {
        static::assertFalse(self::$subject->isReadOnly());
        static::assertTrue(self::$subject->isReadOnly(true));
    }
}
