<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Override;
use Mage;
use Mage_Core_Model_Config as Subject;
use Varien_Db_Adapter_Interface;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\ConfigTrait;

final class ConfigTest extends OpenMageTest
{
    use ConfigTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/config');
    }

    /**
     * @group Model
     */
    public function testSaveDeleteGetConfig(): void
    {
        $path = 'test/config';
        $value = 'foo';

        self::assertFalse(self::$subject->getConfig($path));

        self::$subject->saveConfig($path, $value);
        self::assertSame($value, self::$subject->getConfig($path));

        self::$subject->deleteConfig($path);
        self::assertFalse(self::$subject->getConfig($path));
    }

    /**
     * @group Model
     */
    public function testCleanAndWarmCacheRunsCleanerUnderLockAndSavesConfig(): void
    {
        $config = Mage::app()->getConfig();
        $cacheId = $config->getCacheId();
        $lockHeld = null;

        $result = $config->cleanAndWarmCache(function () use ($cacheId, &$lockHeld): string {
            Mage::app()->cleanCache([Subject::CACHE_TAG]);
            self::assertFalse(Mage::app()->loadCache($cacheId));
            $lockHeld = $this->isConfigCacheSaveLockHeld();

            return 'cleaned';
        });

        self::assertSame('cleaned', $result);
        self::assertTrue($lockHeld);
        self::assertFalse($this->isConfigCacheSaveLockHeld());
        self::assertNotFalse(Mage::app()->loadCache($cacheId));
        self::assertTrue((bool) Mage::app()->useCache('config'));
    }

    /**
     * @group Model
     */
    public function testCleanAndWarmCacheOnlyRunsCleanerWhenConfigCacheIsDisabled(): void
    {
        $cache = Mage::app()->getCacheInstance();
        $cache->banUse('config');

        $lockHeld = null;

        try {
            $result = Mage::app()->getConfig()->cleanAndWarmCache(function () use (&$lockHeld): int {
                $lockHeld = $this->isConfigCacheSaveLockHeld();

                return 42;
            });
        } finally {
            $cache->unbanUse('config');
        }

        self::assertSame(42, $result);
        self::assertFalse($lockHeld);
    }

    private function isConfigCacheSaveLockHeld(): bool
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        self::assertInstanceOf(Varien_Db_Adapter_Interface::class, $connection);

        return (bool) $connection->fetchOne("SELECT IS_USED_LOCK('core_config_cache_save_lock') = CONNECTION_ID()");
    }
}
