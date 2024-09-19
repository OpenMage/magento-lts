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
use Mage_AdminNotification_Model_Feed;
use PHPUnit\Framework\TestCase;

class FeedTest extends TestCase
{
    /**
     * @var Mage_AdminNotification_Model_Feed
     */
    public Mage_AdminNotification_Model_Feed $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('adminnotification/feed');
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testGetFeedUrl(): void
    {
        $this->assertIsString($this->subject->getFeedUrl());
    }

    /**
     * @group Mage_AdminNotification
     * @group Mage_AdminNotification_Model
     */
    public function testCheckUpdate(): void
    {
        $this->assertInstanceOf(Mage_AdminNotification_Model_Feed::class, $this->subject->checkUpdate());
    }
}
