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
use Mage_Log_Model_Cron as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class CronTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('log/cron');
    }

    /**
     * @group Model
     */
    public function testLogClean(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->logClean());
    }
}
