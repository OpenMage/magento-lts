<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Model\System\Config\Source\Log;

use Mage;
use Mage_Core_Helper_Log;
use Mage_Adminhtml_Model_System_Config_Source_Log_Handler as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;

final class HandlerTest extends OpenMageTest
{
    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('adminhtml/system_config_source_log_handler');
    }

    /**
     * @covers Mage_Adminhtml_Model_System_Config_Source_Log_Handler::toOptionArray()
     * @group Model
     */
    public function testToOptionArray(): void
    {
        self::assertSame(
            [
                Mage_Core_Helper_Log::HANDLER_FILE,
                Mage_Core_Helper_Log::HANDLER_SYSLOG,
                Mage_Core_Helper_Log::HANDLER_SYSLOG_UDP,
            ],
            array_keys(self::$subject->toOptionArray()),
        );
    }
}
