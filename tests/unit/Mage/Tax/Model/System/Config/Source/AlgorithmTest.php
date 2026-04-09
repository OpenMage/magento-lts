<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Tax\Model\System\Config\Source;

// use Mage;
// use Mage_Tax_Model_System_Config_Source_Algorithm as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Tax\Model\System\Config\Source\AlgorithmTrait;

final class AlgorithmTest extends OpenMageTest
{
    use AlgorithmTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('tax/system_config_source_algorithm');
        self::markTestSkipped('');
    }
}
