<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model;

use Mage_Core_Exception;
use Mage_Customer_Model_Customer as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer\CustomerTrait;

final class CustomerTest extends OpenMageTest
{
    use CustomerTrait;

    /**
     * @dataProvider provideValidateCustomerData
     * @param  array|true          $expectedResult
     * @throws Mage_Core_Exception
     */
    public function testValidate($expectedResult, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertSame($expectedResult, $mock->validate());
    }
}
