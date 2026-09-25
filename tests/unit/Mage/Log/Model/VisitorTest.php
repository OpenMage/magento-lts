<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Log\Model;

use Mage;
use Mage_Log_Model_Visitor as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class VisitorTest extends OpenMageTest
{
    private static Subject $subject;

    protected function setUp(): void
    {
        self::$subject = Mage::getModel('log/visitor');
    }

    /**
     * @group Model
     */
    public function testInitServerData(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->initServerData());
    }

    /**
     * @group Model
     */
    public function testGetOnlineMinutesInterval(): void
    {
        self::assertIsInt(Subject::getOnlineMinutesInterval());
    }

    /**
     * @group Model
     */
    public function testGetUrl(): void
    {
        self::assertIsString(self::$subject->getUrl());
    }

    /**
     * @group Model
     */
    public function testGetFirstVisitAt(): void
    {
        self::assertIsString(self::$subject->getFirstVisitAt());
    }

    /**
     * @group Model
     */
    public function testGetLastVisitAt(): void
    {
        self::assertIsString(self::$subject->getLastVisitAt());
    }
}
