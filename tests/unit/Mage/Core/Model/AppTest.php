<?php

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Override;
use Mage;
use Mage_Core_Exception;
use Mage_Core_Model_App as Subject;
use Mage_Core_Model_Store;
use Mage_Core_Model_Store_Exception;
use Mage_Core_Model_Store_Group;
use Mage_Core_Model_Website;
use Monolog\Level;
use OpenMage\Tests\Unit\Traits\CaptureLogTrait;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\CoreTrait;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\AppTrait;
use OpenMage\Tests\Unit\OpenMageTest;
use ReflectionMethod;
use stdClass;
use Varien_Event;
use Varien_Event_Observer;

final class AppTest extends OpenMageTest
{
    use AppTrait;

    use CaptureLogTrait;

    use CoreTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        self::$subject = Mage::app();
    }

    protected function tearDown(): void
    {
        $this->restoreLogger();
        parent::tearDown();
    }

    /**
     * @dataProvider provideGetStoreConfigData
     * @dataProvider provideGetStoreData
     * @group Model
     */
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
     * @dataProvider provideGetStoreConfigData
     * @dataProvider provideGetWebsiteData
     * @group Model
     */
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
     * @dataProvider provideGetStoreConfigData
     * @dataProvider provideGetGroupData
     * @group Model
     */
    public function testGetGroup(null|bool|int|Mage_Core_Model_Store_Group|string $id): void
    {
        try {
            self::assertInstanceOf(Mage_Core_Model_Store_Group::class, self::$subject->getGroup($id));
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertNotEmpty($mageCoreException->getMessage());
            self::assertSame('Invalid store group id requested.', $mageCoreException->getMessage());
        }
    }

    /**
     * @covers Mage_Core_Model_App::_callObserverMethod()
     * @group Model
     */
    public function testSkippedObserverIsLoggedOnceOutsideDeveloperMode(): void
    {
        $logger = $this->captureLog();
        $observerName = 'openmagetest_' . uniqid();
        $developerMode = Mage::getIsDeveloperMode();
        Mage::setIsDeveloperMode(false);

        try {
            $this->callObserverMethod(false, 'execute', $observerName);
            $this->callObserverMethod(false, 'execute', $observerName);
            $this->callObserverMethod(new stdClass(), 'missingMethod', $observerName . '_method');
        } finally {
            Mage::setIsDeveloperMode($developerMode);
        }

        self::assertSame(
            [
                [
                    sprintf('Observer "%1$s" for event "openmagetest_event" skipped: Class from observer "%1$s" is not initialized', $observerName),
                    Level::Error,
                ],
                [
                    sprintf('Observer "%s_method" for event "openmagetest_event" skipped: Method "missingMethod" is not defined in "stdClass"', $observerName),
                    Level::Error,
                ],
            ],
            $logger->messages,
        );
    }

    /**
     * @covers Mage_Core_Model_App::_callObserverMethod()
     * @group Model
     */
    public function testSkippedObserverThrowsInDeveloperMode(): void
    {
        $logger = $this->captureLog();
        $observerName = 'openmagetest_' . uniqid();
        $developerMode = Mage::getIsDeveloperMode();
        Mage::setIsDeveloperMode(true);

        try {
            $this->callObserverMethod(false, 'execute', $observerName);
            self::fail('Expected a Mage_Core_Exception');
        } catch (Mage_Core_Exception $mageCoreException) {
            self::assertSame(sprintf('Class from observer "%s" is not initialized', $observerName), $mageCoreException->getMessage());
        } finally {
            Mage::setIsDeveloperMode($developerMode);
        }

        self::assertSame([], $logger->messages);
    }

    private function callObserverMethod(mixed $object, string $method, string $observerName): void
    {
        $event = new Varien_Event();
        $event->setName('openmagetest_event');

        $observer = new Varien_Event_Observer();
        $observer->setData(['event' => $event]);

        (new ReflectionMethod(Subject::class, '_callObserverMethod'))
            ->invoke(self::$subject, $object, $method, $observer, $observerName);
    }
}
