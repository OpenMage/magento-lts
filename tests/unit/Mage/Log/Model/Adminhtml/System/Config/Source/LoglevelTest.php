<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Log\Model\Adminhtml\System\Config\Source;

// use Mage;
// use Mage_Log_Model_Adminhtml_System_Config_Source_Loglevel as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Log\Model\Adminhtml\System\Config\Source\LoglevelTrait;

final class LoglevelTest extends OpenMageTest
{
    use LoglevelTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('log/adminhtml_system_config_source_loglevel');
        self::markTestSkipped('');
    }
}
