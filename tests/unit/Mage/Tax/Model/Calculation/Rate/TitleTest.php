<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Tax\Model\Calculation\Rate;

// use Mage;
// use Mage_Tax_Model_Calculation_Rate_Title as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Tax\Model\Calculation\Rate\TitleTrait;

final class TitleTest extends OpenMageTest
{
    use TitleTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('tax/calculation_rate_title');
        self::markTestSkipped('');
    }
}
