<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Admin\Model;

use Generator;

/**
 * @phpstan-type AuthenticateData array{
 *     "user_id": string,
 *     "username": string,
 *     "password": string,
 *     "is_active": string
 * }
 * @phpstan-type AuthenticateMethods array{
 *      "validatePasswordHash": bool,
 *      "hasAssigned2Role": bool
 * }
 * @phpstan-type ValidateData array{
 *      "new_password": ?string,
 *      "password": ?string
 *  }
 */
trait UserTrait
{
    /**
     * @return Generator<string, list{bool|string, AuthenticateData, AuthenticateMethods}, void, void>
     */
    public static function provideAuthenticateData(): Generator
    {
        $validData = [
            'user_id'  => '999',
            'username' => 'new',
            'password' => 'veryl0ngpassw0rd',
            'is_active' => '1',
        ];
        $validMethods = [
            'validatePasswordHash' => true,
            'hasAssigned2Role' => true,
        ];

        yield 'pass' => [
            true,
            $validData,
            $validMethods,
        ];

        $data = $validData;
        $methods = $validMethods;
        $data['is_active'] = '0';
        yield 'fail #0 inactive' => [
            'This account is inactive.',
            $data,
            $methods,
        ];

        $data = $validData;
        $methods['validatePasswordHash'] = false;
        yield 'fail #1 invalid hash' => [
            false,
            $data,
            $methods,
        ];

        $methods = $validMethods;
        $methods['hasAssigned2Role'] = false;
        yield 'fail #2 no role assigned' => [
            'Access denied.',
            $data,
            $methods,
        ];
    }

    /**
     * @return Generator<string, list{bool|string[], ValidateData}, void, void>
     */
    public static function provideValidateAdminUserData(): Generator
    {
        $errors = [
            'User Name is required field.',
            'First Name is required field.',
            'Last Name is required field.',
            'Please enter a valid email.',
            'Password must be at least of 14 characters.',
            'Password must include both numeric and alphabetic characters.',
        ];
        yield 'fail different passwords' => [
            $errors,
            [
                'new_password' => '123',
            ],
        ];
        yield 'fails #2' => [
            $errors,
            [
                'password' => '456',
            ],
        ];
    }

    public static function provideIsResetPasswordLinkTokenExpiredData(): Generator
    {
        yield 'empty data' => [
            true,
            [
                'rp_token'       => '',
                'rp_token_created_at' => '',
            ],
        ];
        yield '#valid data' => [
            true,
            [
                'rp_token'       => '1',
                'rp_token_created_at' => '2025-01-01 10:20:30',
            ],
        ];
    }
}
