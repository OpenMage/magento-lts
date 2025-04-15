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

use Mage;
use Mage_AdminNotification_Model_Feed as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use SimpleXMLElement;

class FeedTest extends OpenMageTest
{
    private static Subject $subject;

    public function setUp(): void
    {
        self::$subject = Mage::getModel('adminnotification/feed');
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testGetFeedUrl(): void
    {
        static::assertIsString(self::$subject->getFeedUrl());
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testCheckUpdate(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->checkUpdate());
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testGetFeedData(): void
    {
        static::assertInstanceOf(SimpleXMLElement::class, self::$subject->getFeedData());
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testGetFeedXml(): void
    {
        static::assertInstanceOf(SimpleXMLElement::class, self::$subject->getFeedXml());
    }
}
