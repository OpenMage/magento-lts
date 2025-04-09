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

use Generator;
use Mage;
use Mage_Customer_Model_Customer as Subject;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('customer/customer');
    }

    /**
     * @dataProvider provideValidateCustomerData
     * @param array|true $expectedResult
     * @group Mage_Customer_Model
     */
    public function testValidate($expectedResult, array $methods): void
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->setMethods([
                'getFirstname',
                'getLastname',
                'getEmail',
                'getPassword',
                'getPasswordConfirmation',
                'getDob',
                'getTaxvat',
                'getGender',
                'shouldValidateDob',
                'shouldValidateTaxvat',
                'shouldValidateGender',
            ])
            ->getMock();

        $mock->method('getFirstname')->willReturn($methods['getFirstname']);
        $mock->method('getLastname')->willReturn($methods['getLastname']);
        $mock->method('getEmail')->willReturn($methods['getEmail']);
        $mock->method('getPassword')->willReturn($methods['getPassword']);
        $mock->method('getPasswordConfirmation')->willReturn($methods['getPasswordConfirmation']);
        $mock->method('getDob')->willReturn($methods['getDob']);
        $mock->method('getTaxvat')->willReturn($methods['getTaxvat']);
        $mock->method('getGender')->willReturn($methods['getGender']);
        $mock->method('shouldValidateDob')->willReturn($methods['shouldValidateDob']);
        $mock->method('shouldValidateTaxvat')->willReturn($methods['shouldValidateTaxvat']);
        $mock->method('shouldValidateGender')->willReturn($methods['shouldValidateGender']);

        $this->assertSame($expectedResult, $mock->validate());
    }

    public function provideValidateCustomerData(): Generator
    {
        yield 'valid data' => [
            true,
            [
                'getFirstname' => 'John',
                'getLastname' => 'Doe',
                'getEmail' => 'john.doe@example.com',
                'getPassword' => 'validpassword123',
                'getPasswordConfirmation' => 'validpassword123',
                'getDob' => '1980-01-01',
                'getTaxvat' => '123456789',
                'getGender' => '1',
                'shouldValidateDob' => false,
                'shouldValidateTaxvat' => false,
                'shouldValidateGender' => false,
            ],
        ];
        yield 'missing firstname' => [
            ['The first name cannot be empty.'],
            [
                'getFirstname' => '',
                'getLastname' => 'Doe',
                'getEmail' => 'john.doe@example.com',
                'getPassword' => 'validpassword123',
                'getPasswordConfirmation' => 'validpassword123',
                'getDob' => '1980-01-01',
                'getTaxvat' => '123456789',
                'getGender' => '1',
                'shouldValidateDob' => false,
                'shouldValidateTaxvat' => false,
                'shouldValidateGender' => false,
            ],
        ];
        yield 'missing lastname' => [
            ['The last name cannot be empty.'],
            [
                'getFirstname' => 'John',
                'getLastname' => '',
                'getEmail' => 'john.doe@example.com',
                'getPassword' => 'validpassword123',
                'getPasswordConfirmation' => 'validpassword123',
                'getDob' => '1980-01-01',
                'getTaxvat' => '123456789',
                'getGender' => '1',
                'shouldValidateDob' => false,
                'shouldValidateTaxvat' => false,
                'shouldValidateGender' => false,
            ],
        ];
        yield 'missing email' => [
            ['Invalid email address "".'],
            [
                'getFirstname' => 'John',
                'getLastname' => 'Doe',
                'getEmail' => '',
                'getPassword' => 'validpassword123',
                'getPasswordConfirmation' => 'validpassword123',
                'getDob' => '1980-01-01',
                'getTaxvat' => '123456789',
                'getGender' => '1',
                'shouldValidateDob' => false,
                'shouldValidateTaxvat' => false,
                'shouldValidateGender' => false,
            ],
        ];
        yield 'invalid email' => [
            ['Invalid email address "invalid-email".'],
            [
                'getFirstname' => 'John',
                'getLastname' => 'Doe',
                'getEmail' => 'invalid-email',
                'getPassword' => 'validpassword123',
                'getPasswordConfirmation' => 'validpassword123',
                'getDob' => '1980-01-01',
                'getTaxvat' => '123456789',
                'getGender' => '1',
                'shouldValidateDob' => false,
                'shouldValidateTaxvat' => false,
                'shouldValidateGender' => false,
            ],
        ];
        yield 'passwords do not match' => [
            ['Please make sure your passwords match.'],
            [
                'getFirstname' => 'John',
                'getLastname' => 'Doe',
                'getEmail' => 'john.doe@example.com',
                'getPassword' => 'validpassword123',
                'getPasswordConfirmation' => 'differentpassword',
                'getDob' => '1980-01-01',
                'getTaxvat' => '123456789',
                'getGender' => '1',
                'shouldValidateDob' => false,
                'shouldValidateTaxvat' => false,
                'shouldValidateGender' => false,
            ],
        ];
        yield 'passwords to short' => [
            ['The minimum password length is 7'],
            [
                'getFirstname' => 'John',
                'getLastname' => 'Doe',
                'getEmail' => 'john.doe@example.com',
                'getPassword' => '123',
                'getPasswordConfirmation' => '123',
                'getDob' => '1980-01-01',
                'getTaxvat' => '123456789',
                'getGender' => '1',
                'shouldValidateDob' => false,
                'shouldValidateTaxvat' => false,
                'shouldValidateGender' => false,
            ],
        ];
        yield 'passwords to long' => [
            ['Please enter a password with at most 256 characters.'],
            [
                'getFirstname' => 'John',
                'getLastname' => 'Doe',
                'getEmail' => 'john.doe@example.com',
                'getPassword' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
                'getPasswordConfirmation' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
                'getDob' => '1980-01-01',
                'getTaxvat' => '123456789',
                'getGender' => '1',
                'shouldValidateDob' => false,
                'shouldValidateTaxvat' => false,
                'shouldValidateGender' => false,
            ],
        ];
        yield 'missing dob' => [
            ['The Date of Birth is required.'],
            [
                'getFirstname' => 'John',
                'getLastname' => 'Doe',
                'getEmail' => 'john.doe@example.com',
                'getPassword' => 'validpassword123',
                'getPasswordConfirmation' => 'validpassword123',
                'getDob' => '',
                'getTaxvat' => '123456789',
                'getGender' => '1',
                'shouldValidateDob' => true,
                'shouldValidateTaxvat' => false,
                'shouldValidateGender' => false,
            ],
        ];
        yield 'invalid dob' => [
            ['This value is not a valid date.'],
            [
                'getFirstname' => 'John',
                'getLastname' => 'Doe',
                'getEmail' => 'john.doe@example.com',
                'getPassword' => 'validpassword123',
                'getPasswordConfirmation' => 'validpassword123',
                'getDob' => 'abc',
                'getTaxvat' => '123456789',
                'getGender' => '1',
                'shouldValidateDob' => true,
                'shouldValidateTaxvat' => false,
                'shouldValidateGender' => false,
            ],
        ];
        yield 'missing taxvat' => [
            ['The TAX/VAT number is required.'],
            [
                'getFirstname' => 'John',
                'getLastname' => 'Doe',
                'getEmail' => 'john.doe@example.com',
                'getPassword' => 'validpassword123',
                'getPasswordConfirmation' => 'validpassword123',
                'getDob' => '1980-01-01',
                'getTaxvat' => '',
                'getGender' => '1',
                'shouldValidateDob' => false,
                'shouldValidateTaxvat' => true,
                'shouldValidateGender' => false,
            ],
        ];
        yield 'missing gender' => [
            ['Gender is required.'],
            [
                'getFirstname' => 'John',
                'getLastname' => 'Doe',
                'getEmail' => 'john.doe@example.com',
                'getPassword' => 'validpassword123',
                'getPasswordConfirmation' => 'validpassword123',
                'getDob' => '1980-01-01',
                'getTaxvat' => '123456789',
                'getGender' => '',
                'shouldValidateDob' => false,
                'shouldValidateTaxvat' => false,
                'shouldValidateGender' => true,
            ],
        ];
    }
}
