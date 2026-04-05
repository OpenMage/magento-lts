<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Eav\Model\Adminhtml\System\Config\Source\Inputtype;

use Mage;
use Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class ValidatorTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('eav/adminhtml_system_config_source_inputtype_validator');
    }
}
