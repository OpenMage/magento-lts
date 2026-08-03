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

        yield 'missing firstname' => [
            ['The first name cannot be empty.'],
            array_merge($validCustomer, ['firstname' => '']),
            $validMethods,
        ];

        yield 'missing lastname' => [
            ['The last name cannot be empty.'],
            array_merge($validCustomer, ['lastname' => '']),
            $validMethods,
        ];

        yield 'missing email' => [
            ['Invalid email address "".'],
            array_merge($validCustomer, ['email' => '']),
            $validMethods,
        ];

        yield 'invalid email' => [
            ['Invalid email address "invalid-email".'],
            array_merge($validCustomer, ['email' => 'invalid-email']),
            $validMethods,
        ];

        yield 'passwords do not match' => [
            ['Please make sure your passwords match.'],
            array_merge($validCustomer, ['password_confirmation' => 'differentpassword']),
            $validMethods,
        ];

        $password = '123';
        yield 'passwords to short' => [
            [
                'The minimum password length is 7',
                'Password must include both numeric and alphabetic characters.',
            ],
            array_merge($validCustomer, [
                'password' => $password,
                'password_confirmation' => $password,
            ]),
            $validMethods,
        ];

        $password = str_repeat('x', 257);
        yield 'passwords to long' => [
            [
                'Please enter a password with at most 256 characters.',
                'Password must include both numeric and alphabetic characters.',
            ],
            array_merge($validCustomer, [
                'password' => $password,
                'password_confirmation' => $password,
            ]),
            $validMethods,
        ];

        yield 'missing dob' => [
            ['The Date of Birth is required.'],
            array_merge($validCustomer, ['dob' => '']),
            array_merge($validMethods, ['shouldValidateDob' => true]),
        ];

        yield 'invalid dob' => [
            ['The Date of Birth is not a valid date.'],
            array_merge($validCustomer, ['dob' => 'abc']),
            array_merge($validMethods, ['shouldValidateDob' => true]),
        ];

        yield 'missing taxvat' => [
            ['The TAX/VAT number is required.'],
            array_merge($validCustomer, ['taxvat' => '']),
            array_merge($validMethods, ['shouldValidateTaxvat' => true]),
        ];

        yield 'missing gender' => [
            ['Gender is required.'],
            array_merge($validCustomer, ['gender' => '']),
            array_merge($validMethods, ['shouldValidateGender' => true]),
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
