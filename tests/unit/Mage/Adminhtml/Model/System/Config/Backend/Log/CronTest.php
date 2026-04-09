<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Model\System\Config\Backend\Log;

// use Mage;
// use Mage_Adminhtml_Model_System_Config_Backend_Log_Cron as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Model\System\Config\Backend\Log\CronTrait;

final class CronTest extends OpenMageTest
{
    use CronTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('adminhtml/system_config_backend_log_cron');
        self::markTestSkipped('');
    }
}
