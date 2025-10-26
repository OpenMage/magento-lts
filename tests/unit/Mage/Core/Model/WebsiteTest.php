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
        self::assertInstanceOf(Subject::class, self::$subject->load(1));
        self::assertInstanceOf(Subject::class, self::$subject->load('default'));
    }

    /**
     * @group Model
     */
    public function testLoadConfig(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->loadConfig('1'));
        self::assertInstanceOf(Subject::class, self::$subject->loadConfig('default'));
    }

    /**
     * @group Model
     */
    public function testGetStoreCollection(): void
    {
        self::assertInstanceOf(Mage_Core_Model_Resource_Store_Collection::class, self::$subject->getStoreCollection());
    }

    /**
     * @group Model
     */
    public function testGetGroupCollection(): void
    {
        self::assertInstanceOf(Mage_Core_Model_Resource_Store_Group_Collection::class, self::$subject->getGroupCollection());
    }

    /**
     * @group Model
     */
    public function testGetStores(): void
    {
        self::assertIsArray(self::$subject->getStores());
    }

    /**
     * @group Model
     */
    public function testGetStoreIds(): void
    {
        self::assertIsArray(self::$subject->getStoreIds());
    }

    /**
     * @group Model
     */
    public function testGetStoreCodes(): void
    {
        self::assertIsArray(self::$subject->getStoreCodes());
    }

    /**
     * @group Model
     */
    public function testGetStoresCount(): void
    {
        self::assertIsInt(self::$subject->getStoresCount());
    }

    /**
     * @group Model
     */
    public function testGetGroups(): void
    {
        self::assertIsArray(self::$subject->getGroups());
    }

    /**
     * @group Model
     */
    public function testGetGroupIds(): void
    {
        self::assertIsArray(self::$subject->getGroupIds());
    }

    /**
     * @group Model
     */
    public function testGetGroupsCount(): void
    {
        self::assertIsInt(self::$subject->getGroupsCount());
    }

    /**
     * @group Model
     */
    public function testGetBaseCurrency(): void
    {
        self::assertIsObject(self::$subject->getBaseCurrency());
        self::assertInstanceOf(Mage_Directory_Model_Currency::class, self::$subject->getBaseCurrency());
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
        self::assertIsObject(self::$subject->getDefaultStoresSelect());
        self::assertInstanceOf(Varien_Db_Select::class, self::$subject->getDefaultStoresSelect(true));
    }

    /**
     * @group Model
     */
    public function testIsReadOnly(): void
    {
        self::assertFalse(self::$subject->isReadOnly());
        self::assertTrue(self::$subject->isReadOnly(true));
    }
}
