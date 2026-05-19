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
use Mage_AdminNotification_Model_Feed as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use SimpleXMLElement;

final class FeedTest extends OpenMageTest
{
    private static Subject $subject;

    protected function setUp(): void
    {
        self::$subject = Mage::getModel('adminnotification/feed');
    }

    /**
     * @group Model
     */
    public function testGetFeedUrl(): void
    {
        self::assertIsString(self::$subject->getFeedUrl());
    }

    /**
     * @group Model
     */
    public function testCheckUpdate(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->checkUpdate());
    }

    /**
     * @group Model
     */
    public function testGetFeedData(): void
    {
        self::assertInstanceOf(SimpleXMLElement::class, self::$subject->getFeedData());
    }

    /**
     * @group Model
     */
    public function testGetFeedXml(): void
    {
        self::assertInstanceOf(SimpleXMLElement::class, self::$subject->getFeedXml());
    }
}
