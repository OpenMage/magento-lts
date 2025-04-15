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
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer;

use Generator;

trait CustomerTrait
{
    public function provideValidateCustomerData(): Generator
    {
        $validCustomer = [
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
        ];

        yield 'valid data' => [
            true,
            $validCustomer,
        ];

        $data = $validCustomer;
        $data['getFirstname'] = '';
        yield 'missing firstname' => [
            ['The first name cannot be empty.'],
            $data,
        ];

        $data = $validCustomer;
        $data['getLastname'] = '';
        yield 'missing lastname' => [
            ['The last name cannot be empty.'],
            $data,
        ];

        $data = $validCustomer;
        $data['getEmail'] = '';
        yield 'missing email' => [
            ['Invalid email address "".'],
            $data,
        ];

        $data = $validCustomer;
        $data['getEmail'] = 'invalid-email';
        yield 'invalid email' => [
            ['Invalid email address "invalid-email".'],
            $data,
        ];

        $data = $validCustomer;
        $data['getPasswordConfirmation'] = 'differentpassword';
        yield 'passwords do not match' => [
            ['Please make sure your passwords match.'],
            $data,
        ];

        $data = $validCustomer;
        $password = '123';
        $data['getPassword'] = $password;
        $data['getPasswordConfirmation'] = $password;
        yield 'passwords to short' => [
            ['The minimum password length is 7'],
            $data,
        ];

        $data = $validCustomer;
        $password = str_repeat('x', 257);
        $data['getPassword'] = $password;
        $data['getPasswordConfirmation'] = $password;
        yield 'passwords to long' => [
            ['Please enter a password with at most 256 characters.'],
            $data,
        ];

        $data = $validCustomer;
        $data['getDob'] = '';
        $data['shouldValidateDob'] = true;
        yield 'missing dob' => [
            ['The Date of Birth is required.'],
            $data,
        ];

        $data = $validCustomer;
        $data['getDob'] = 'abc';
        $data['shouldValidateDob'] = true;
        yield 'invalid dob' => [
            ['This value is not a valid date.'],
            $data,
        ];

        $data = $validCustomer;
        $data['getTaxvat'] = '';
        $data['shouldValidateTaxvat'] = true;
        yield 'missing taxvat' => [
            ['The TAX/VAT number is required.'],
            $data,
        ];

        $data = $validCustomer;
        $data['getGender'] = '';
        $data['shouldValidateGender'] = true;
        yield 'missing gender' => [
            ['Gender is required.'],
            $data,
        ];
    }
}
