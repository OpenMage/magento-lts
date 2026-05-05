<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use Override;
use Mage;
use Mage_Core_Exception;
use Mage_Core_Model_App as Subject;
use Mage_Core_Model_Store;
use Mage_Core_Model_Store_Exception;
use Mage_Core_Model_Store_Group;
use Mage_Core_Model_Website;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\CoreTrait;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\AppTrait;
use OpenMage\Tests\Unit\OpenMageTest;

final class AppTest extends OpenMageTest
{
    use AppTrait;

    use CoreTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        self::$subject = Mage::app();
    }

    /**
     * @group Model
     */
    #[DataProvider('provideGetStoreConfigData')]
    #[DataProvider('provideGetStoreData')]
    public function testGetStore(null|bool|int|Mage_Core_Model_Store|string $id): void
    {
        try {
            self::assertInstanceOf(Mage_Core_Model_Store::class, self::$subject->getStore($id));
        } catch (Mage_Core_Model_Store_Exception $mageCoreModelStoreException) {
            self::assertNotEmpty($mageCoreModelStoreException->getMessage());
            self::assertSame('Invalid store code requested.', $mageCoreModelStoreException->getMessage());
        }
    }

    /**
     * @group Model
     */
    #[DataProvider('provideGetStoreConfigData')]
    #[DataProvider('provideGetWebsiteData')]
    public function testGetWebsite(null|bool|int|Mage_Core_Model_Website|string $id): void
    {
        try {
            self::assertInstanceOf(Mage_Core_Model_Website::class, self::$subject->getWebsite($id));
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertNotEmpty($mageCoreException->getMessage());
            self::assertSame('Invalid website id requested.', $mageCoreException->getMessage());
        }
    }

    /**
     * @group Model
     */
    #[DataProvider('provideGetStoreConfigData')]
    #[DataProvider('provideGetGroupData')]
    public function testGetGroup(null|bool|int|Mage_Core_Model_Store_Group|string $id): void
    {
        try {
            self::assertInstanceOf(Mage_Core_Model_Store_Group::class, self::$subject->getGroup($id));
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertNotEmpty($mageCoreException->getMessage());
            self::assertSame('Invalid store group id requested.', $mageCoreException->getMessage());
        }
    }
}
