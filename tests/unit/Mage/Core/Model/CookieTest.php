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
     */
    public function testSetStore(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->setStore(null));
    }

    /**
     * @group Model
     */
    public function testGetStore(): void
    {
        self::assertInstanceOf(Mage_Core_Model_Store::class, self::$subject->getStore());
    }

    /**
     * @group Model
     */
    public function testGetDomain(): void
    {
        self::assertFalse(self::$subject->getDomain());
    }

    /**
     * @group Model
     */
    public function testGetConfigDomain(): void
    {
        self::assertIsString(self::$subject->getConfigDomain());
    }

    /**
     * @group Model
     */
    public function testGetPath(): void
    {
        self::assertIsString(self::$subject->getPath());
    }

    /**
     * @group Model
     */
    public function testGetLifetime(): void
    {
        self::assertIsNumeric(self::$subject->getLifetime());
    }

    /**
     * @group Model
     */
    public function testSetLifetime(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->setLifetime(0));
    }

    /**
     * @group Model
     */
    public function testGetHttponly(): void
    {
        self::assertIsBool(self::$subject->getHttponly());
    }

    /**
     * @group Model
     */
    public function testGetSameSite(): void
    {
        self::assertIsString(self::$subject->getSameSite());
    }

    /**
     * @group Model
     */
    public function testIsSecure(): void
    {
        self::assertIsBool(self::$subject->isSecure());
    }

    /**
     * @group Model
     * @runInSeparateProcess
     */
    public function testSet(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->set('test', 'value'));
    }

    /**
     * @group Model
     */
    public function testRenew(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->renew('test'));
    }

    /**
     * @group Model
     */
    public function testGet(): void
    {
        self::assertIsArray(self::$subject->get());
    }

    /**
     * @group Model
     * @runInSeparateProcess
     */
    public function testDelete(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->delete('test'));
    }
}
