<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Tax\Model\Calculation;

// use Mage;
// use Mage_Tax_Model_Calculation_Rule as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Tax\Model\Calculation\RuleTrait;

final class RuleTest extends OpenMageTest
{
    use RuleTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('tax/calculation_rule');
        self::markTestSkipped('');
    }
}
