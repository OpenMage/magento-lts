<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Cron\Model\Resource;

use Mage;
use Mage_Cron_Model_Resource_Schedule as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Cron\Model\Resource\ScheduleTrait;

final class ScheduleTest extends OpenMageTest
{
    use ScheduleTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('cron/resource_schedule');
        self::markTestSkipped('');
    }
}
