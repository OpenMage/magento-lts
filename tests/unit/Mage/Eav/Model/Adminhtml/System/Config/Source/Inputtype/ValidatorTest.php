<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Eav\Model\Adminhtml\System\Config\Source\Inputtype;

use Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype_Validator as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Eav\Model\Adminhtml\System\Config\Source\Inputtype\ValidatorTrait;

final class ValidatorTest extends OpenMageTest
{
    use ValidatorTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
