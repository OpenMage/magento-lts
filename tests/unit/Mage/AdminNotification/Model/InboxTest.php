<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\AdminNotification\Model;

use Generator;
use Mage;
use Mage_AdminNotification_Model_Inbox as Subject;
use Mage_Core_Exception;
use PHPUnit\Framework\TestCase;

class InboxTest extends TestCase
{
    public const TITLE = 'PhpUnit test';

    public const URL = 'https://openmage.org';

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('adminnotification/inbox');
    }

    /**
     * @dataProvider provideGetSeverities
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testGetSeverities($expectedResult, ?int $severity): void
    {
        $this->assertSame($expectedResult, $this->subject->getSeverities($severity));
    }

    public function provideGetSeverities(): Generator
    {
        yield 'null' => [
            [
                Subject::SEVERITY_CRITICAL  => 'critical',
                Subject::SEVERITY_MAJOR     => 'major',
                Subject::SEVERITY_MINOR     => 'minor',
                Subject::SEVERITY_NOTICE    => 'notice',
            ],
            null
        ];
        yield 'valid' => [
            'critical',
            Subject::SEVERITY_CRITICAL
        ];
        yield 'invalid' => [
            null,
            0
        ];
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testLoadLatestNotice(bool $delete = false): void
    {
        $result = $this->subject->loadLatestNotice();
        $this->assertInstanceOf(Subject::class, $result);
        if ($delete) {
            $result->delete();
        }
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testAdd(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->add(
            Subject::SEVERITY_CRITICAL,
            self::TITLE,
            [__METHOD__],
            self::URL
        ));
        $this->testLoadLatestNotice(true);
    }

    /**
     * @covers Mage_AdminNotification_Model_Inbox::add()
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testAddException(): void
    {
        try {
            $this->subject->add(0, self::TITLE, __METHOD__);
        } catch (Mage_Core_Exception $e) {
            $this->assertSame('Wrong message type', $e->getMessage());
        }
    }

    /**
     * @covers Mage_AdminNotification_Model_Inbox::addCritical()
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testAddCritical(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->addCritical(self::TITLE, __METHOD__));
        $this->testLoadLatestNotice(true);
    }

    /**
     * @covers Mage_AdminNotification_Model_Inbox::addMajor()
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testAddMajor(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->addMajor(self::TITLE, __METHOD__));
        $this->testLoadLatestNotice(true);
    }

    /**
     * @covers Mage_AdminNotification_Model_Inbox::addMinor()
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testAddMinor(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->addMinor(self::TITLE, __METHOD__));
        $this->testLoadLatestNotice(true);
    }

    /**
     * @covers Mage_AdminNotification_Model_Inbox::addNotice()
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testAddNotice(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->addNotice(self::TITLE, __METHOD__));
        $this->testLoadLatestNotice(true);
    }
}
