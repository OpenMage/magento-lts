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
use Mage_Core_Model_Logger;
use Monolog\Level;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\CaptureLogTrait;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\ConfigTrait;

final class ConfigTest extends OpenMageTest
{
    use CaptureLogTrait;

    use ConfigTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/config');
    }

    protected function tearDown(): void
    {
        $this->restoreLogger();
        parent::tearDown();
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
     * @covers Mage_Core_Model_Config::getModelInstance()
     * @group Model
     */
    public function testGetModelInstanceLogsMissingClassOnce(): void
    {
        $logger = $this->captureLog();
        $config = Mage::getConfig();
        self::assertInstanceOf(Subject::class, $config);
        $alias = 'openmagetest/missing_' . uniqid();
        $className = $config->getModelClassName($alias);

        self::assertFalse($config->getModelInstance($alias));
        self::assertFalse($config->getModelInstance($alias));

        self::assertSame(
            [[sprintf('Model class "%s" for "%s" not found', $className, $alias), Level::Warning]],
            $logger->messages,
        );
    }

    /**
     * @covers Mage_Core_Model_Config::getModelInstance()
     * @group Model
     */
    public function testGetModelInstanceDoesNotLogExistingClass(): void
    {
        $logger = $this->captureLog();
        $config = Mage::getConfig();
        self::assertInstanceOf(Subject::class, $config);

        self::assertInstanceOf(Mage_Core_Model_Logger::class, $config->getModelInstance('core/logger'));
        self::assertSame([], $logger->messages);
    }
}
