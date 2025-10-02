<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Cookie as Subject;
use Mage_Core_Model_Store;
use OpenMage\Tests\Unit\OpenMageTest;

final class CookieTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/cookie');
    }

    /**
     * @group Model
     * @group test
     */
    public function testSetStore(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->setStore(null));
    }

    /**
     * @group Model
     * @group test
     */
    public function testGetStore(): void
    {
        self::assertInstanceOf(Mage_Core_Model_Store::class, self::$subject->getStore());
    }

    /**
     * @group Model
     * @group test
     */
    public function testGetDomain(): void
    {
        self::assertFalse(self::$subject->getDomain());
    }

    /**
     * @group Model
     * @group test
     */
    public function testGetConfigDomain(): void
    {
        self::assertIsString(self::$subject->getConfigDomain());
    }

    /**
     * @group Model
     * @group test
     */
    public function testGetPath(): void
    {
        self::assertIsString(self::$subject->getPath());
    }

    /**
     * @group Model
     * @group test
     */
    public function testGetLifetime(): void
    {
        self::assertIsNumeric(self::$subject->getLifetime());
    }

    /**
     * @group Model
     * @group test
     */
    public function testSetLifetime(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->setLifetime(0));
    }

    /**
     * @group Model
     * @group test
     */
    public function testGetHttponly(): void
    {
        self::assertIsBool(self::$subject->getHttponly());
    }

    /**
     * @group Model
     * @group test
     */
    public function testGetSameSite(): void
    {
        self::assertIsString(self::$subject->getSameSite());
    }

    /**
     * @group Model
     * @group test
     */
    public function testIsSecure(): void
    {
        self::assertIsBool(self::$subject->isSecure());
    }

    /**
     * @group Model
     * @group test
     * @runInSeparateProcess
     */
    public function testSet(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->set('test', 'value'));
    }

    /**
     * @group Model
     * @group test
     */
    public function testRenew(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->renew('test'));
    }

    /**
     * @group Model
     * @group test
     */
    public function testGet(): void
    {
        self::assertIsArray(self::$subject->get());
    }

    /**
     * @group Model
     * @group test
     * @runInSeparateProcess
     */
    public function testDelete(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->delete('test'));
    }
}
