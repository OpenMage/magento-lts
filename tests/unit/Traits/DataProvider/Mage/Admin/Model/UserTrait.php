<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Admin\Model;

use Generator;
use Varien_Date;

trait UserTrait
{
    public function provideAuthenticateData(): Generator
    {
        $validData = [
            'getId'       => '999',
            'getUsername' => 'new',
            'getPassword' => 'veryl0ngpassw0rd',
            'getIsActive' => '1',
            'validatePasswordHash' => true,
            'hasAssigned2Role' => true,
        ];

        yield 'pass' => [
            true,
            $validData,
        ];

        $data = $validData;
        $data['getUsername'] = 'admin';
        yield 'fail #0 account exists' => [
            'User Name already exists.',
            $data,
        ];

        $data = $validData;
        $data['getIsActive'] = '0';
        yield 'fail #1 inactive' => [
            'This account is inactive.',
            $data,
        ];

        $data = $validData;
        $data['validatePasswordHash'] = false;
        yield 'fail #2 invalid hash' => [
            false,
            $data,
        ];

        $data = $validData;
        $data['hasAssigned2Role'] = false;
        yield 'fail #3 no role assigned' => [
            'Access denied.',
            $data,
        ];
    }

    public function provideValidateAdminUserData(): Generator
    {
        yield 'fail different passwords' => [
            [
                0 => 'User Name is required field.',
                1 => 'First Name is required field.',
                2 => 'Last Name is required field.',
                3 => 'Please enter a valid email.',
                4 => 'Password must be at least of 14 characters.',
                5 => 'Password must include both numeric and alphabetic characters.',
            ],
            [
                'hasNewPassword' => true,
                'getNewPassword' => '123',
                'hasPassword' => false,
                'getPassword' => '456',
            ],
        ];
        yield 'fails #2' => [
            [
                0 => 'User Name is required field.',
                1 => 'First Name is required field.',
                2 => 'Last Name is required field.',
                3 => 'Please enter a valid email.',
                4 => 'Password must be at least of 14 characters.',
                5 => 'Password must include both numeric and alphabetic characters.',
            ],
            [
                'hasNewPassword' => false,
                'getNewPassword' => '123',
                'hasPassword' => true,
                'getPassword' => '456',
            ],
        ];
    }

    public function provideIsResetPasswordLinkTokenExpiredData(): Generator
    {
        yield 'empty data' => [
            true,
            [
                'getRpToken'       => '',
                'getRpTokenCreatedAt' => '',
            ],
        ];
        yield '#valid data' => [
            true,
            [
                'getRpToken'       => '1',
                'getRpTokenCreatedAt' => '2025-01-01 10:20:30',
            ],
        ];
    }
}
