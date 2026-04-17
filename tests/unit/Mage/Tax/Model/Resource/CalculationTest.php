<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Tax\Model\Resource;

use Mage;
use Mage_Tax_Model_Resource_Calculation as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class CalculationTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getResourceModel('tax/calculation');
    }

    /**
     * @group Model
     */
    public function testGetCalculationProcess(): void
    {
        self::assertIsArray(self::$subject->getCalculationProcess(null));
    }
}
