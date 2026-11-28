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
use Mage_Adminhtml_Model_System_Config_Source_Log_Facility as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;

final class FacilityTest extends OpenMageTest
{
    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('adminhtml/system_config_source_log_facility');
    }

    /**
     * @covers Mage_Adminhtml_Model_System_Config_Source_Log_Facility::toOptionArray()
     * @group Model
     */
    public function testToOptionArray(): void
    {
        self::assertSame(
            ['user', 'local0', 'local1', 'local2', 'local3', 'local4', 'local5', 'local6', 'local7'],
            array_keys(self::$subject->toOptionArray()),
        );
    }
}
