<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Helper;

use Generator;

trait ConfigTrait
{
    public static array $backendModel = [
        'color' => 'adminhtml/system_config_backend_color',
    ];

    public function provideGetInputTypes(): Generator
    {
        yield 'null' => [
            [
                'color' => [
                    'backend_model' => self::$backendModel['color'],
                ],
            ],
            null,
        ];
        yield 'color' => [
            [
                'backend_model' => self::$backendModel['color'],
            ],
            'color',
        ];
        yield 'invalid' => [
            [],
            'invalid',
        ];
    }

    public function provideGetBackendModelByInputType(): Generator
    {
        yield 'color' => [
            self::$backendModel['color'],
            'color',
        ];
        yield 'invalid' => [
            null,
            'invalid',
        ];
    }
}
