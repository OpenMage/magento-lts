<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\AdminNotification\Model;

use Mage;
use Mage_AdminNotification_Model_Inbox as Subject;
use Mage_Core_Exception;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\AdminNotification\Model\InboxTrait;

final class InboxTest extends OpenMageTest
{
    use InboxTrait;

    public const TITLE = 'PhpUnit test';

    public const URL = 'https://openmage.org';

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('adminnotification/inbox');
    }

    /**
     * @dataProvider provideGetSeverities
     * @group Model
     */
    public function testGetSeverities(array|string|null $expectedResult, ?int $severity): void
    {
        self::assertSame($expectedResult, self::$subject->getSeverities($severity));
    }

    /**
     * @group Model
     */
    public function testLoadLatestNotice(bool $delete = false): void
    {
        $result = self::$subject->loadLatestNotice();
        self::assertInstanceOf(Subject::class, $result);
        if ($delete) {
            $result->delete();
        }
    }

    /**
     * @group Model
     */
    public function testAdd(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->add(
            Subject::SEVERITY_CRITICAL,
            self::TITLE,
            [__METHOD__],
            self::URL,
        ));
        $this->testLoadLatestNotice(true);
    }

    /**
     * @covers Mage_AdminNotification_Model_Inbox::add()
     * @group Model
     */
    public function testAddException(): void
    {
        try {
            self::$subject->add(0, self::TITLE, __METHOD__);
        } catch (Mage_Core_Exception $e) {
            self::assertSame('Wrong message type', $e->getMessage());
        }
    }

    /**
     * @covers Mage_AdminNotification_Model_Inbox::addCritical()
     * @group Model
     */
    public function testAddCritical(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->addCritical(self::TITLE, __METHOD__));
        $this->testLoadLatestNotice(true);
    }

    /**
     * @covers Mage_AdminNotification_Model_Inbox::addMajor()
     * @group Model
     */
    public function testAddMajor(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->addMajor(self::TITLE, __METHOD__));
        $this->testLoadLatestNotice(true);
    }

    /**
     * @covers Mage_AdminNotification_Model_Inbox::addMinor()
     * @group Model
     */
    public function testAddMinor(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->addMinor(self::TITLE, __METHOD__));
        $this->testLoadLatestNotice(true);
    }

    /**
     * @covers Mage_AdminNotification_Model_Inbox::addNotice()
     * @group Model
     */
    public function testAddNotice(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->addNotice(self::TITLE, __METHOD__));
        $this->testLoadLatestNotice(true);
    }
}
