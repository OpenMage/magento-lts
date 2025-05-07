<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model;

use Mage_Core_Exception;
use Mage_Customer_Model_Customer as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer\CustomerTrait;
use Zend_Validate_Exception;

class CustomerTest extends OpenMageTest
{
    use CustomerTrait;

    /**
     * @dataProvider provideValidateCustomerData
     * @param array|true $expectedResult
     * @throws Mage_Core_Exception
     * @throws Zend_Validate_Exception
     */
    public function testValidate($expectedResult, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        static::assertInstanceOf(Subject::class, $mock);
        static::assertSame($expectedResult, $mock->validate());
    }
}
