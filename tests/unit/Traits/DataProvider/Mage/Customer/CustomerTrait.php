<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer;

use Generator;

/**
 * @phpstan-type ValidateData array{
 *     "firstname": string,
 *     "lastname": string,
 *     "email": string,
 *     "password": string,
 *     "password_confirmation": string,
 *     "dob": string,
 *     "taxvat": string,
 *     "gender": string,
 *     "is_change_password": bool
 * }
 *
 * @phpstan-type ValidateMethods array{
 *     "shouldValidateDob": bool,
 *     "shouldValidateTaxvat": bool,
 *     "shouldValidateGender": bool
 * }
 */
trait CustomerTrait
{
    /**
     * @return Generator<string, list{bool|string[], ValidateData, ValidateMethods}, void, void>
     */
    public static function provideValidateCustomerData(): Generator
    {
        $validCustomer = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'validpassword123',
            'password_confirmation' => 'validpassword123',
            'dob' => '1981-01-01 00:00:00',
            'taxvat' => '123456789',
            'gender' => '1',
            'is_change_password' => true,
        ];
        $validMethods = [
            'shouldValidateDob' => false,
            'shouldValidateTaxvat' => false,
            'shouldValidateGender' => false,
        ];

        yield 'valid data' => [
            true,
            $validCustomer,
            $validMethods,
        ];

        $data = $validCustomer;
        $data['firstname'] = '';
        yield 'missing firstname' => [
            ['The first name cannot be empty.'],
            $data,
            $validMethods,
        ];

        $data = $validCustomer;
        $data['lastname'] = '';
        yield 'missing lastname' => [
            ['The last name cannot be empty.'],
            $data,
            $validMethods,
        ];

        $data = $validCustomer;
        $data['email'] = '';
        yield 'missing email' => [
            ['Invalid email address "".'],
            $data,
            $validMethods,
        ];

        $data = $validCustomer;
        $data['email'] = 'invalid-email';
        yield 'invalid email' => [
            ['Invalid email address "invalid-email".'],
            $data,
            $validMethods,
        ];

        $data = $validCustomer;
        $data['password_confirmation'] = 'differentpassword';
        yield 'passwords do not match' => [
            ['Please make sure your passwords match.'],
            $data,
            $validMethods,
        ];

        $data = $validCustomer;
        $password = '123';
        $data['password'] = $password;
        $data['password_confirmation'] = $password;
        yield 'passwords to short' => [
            [
                'The minimum password length is 7',
                'Password must include both numeric and alphabetic characters.',
            ],
            $data,
            $validMethods,
        ];

        $data = $validCustomer;
        $password = str_repeat('x', 257);
        $data['password'] = $password;
        $data['password_confirmation'] = $password;
        yield 'passwords to long' => [
            [
                'Please enter a password with at most 256 characters.',
                'Password must include both numeric and alphabetic characters.',
            ],
            $data,
            $validMethods,
        ];

        $data = $validCustomer;
        $data['dob'] = '';
        $methods = $validMethods;
        $methods['shouldValidateDob'] = true;
        yield 'missing dob' => [
            ['The Date of Birth is required.'],
            $data,
            $methods,
        ];

        $data = $validCustomer;
        $data['dob'] = 'abc';
        $methods = $validMethods;
        $methods['shouldValidateDob'] = true;
        yield 'invalid dob' => [
            ['The Date of Birth is not a valid date.'],
            $data,
            $methods,
        ];

        $data = $validCustomer;
        $data['taxvat'] = '';
        $methods = $validMethods;
        $methods['shouldValidateTaxvat'] = true;
        yield 'missing taxvat' => [
            ['The TAX/VAT number is required.'],
            $data,
            $methods,
        ];

        $data = $validCustomer;
        $data['gender'] = '';
        $methods = $validMethods;
        $methods['shouldValidateGender'] = true;
        yield 'missing gender' => [
            ['Gender is required.'],
            $data,
            $methods,
        ];
    }

    public static function provideGetDobData(): Generator
    {
        $result = '1981-01-01 00:00:00';

        yield 'null' => [
            null,
            null,
        ];
        yield 'empty' => [
            '',
            '',
        ];
        yield 'date' => [
            $result,
            '1981-01-01',
        ];
        yield 'datetime' => [
            $result,
            '1981-01-01 23:59:00',
        ];
    }
}
