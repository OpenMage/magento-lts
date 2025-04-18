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

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Helper;

use Generator;

trait ConfigTrait
{
    public function provideGetInputTypes(): Generator
    {
        yield 'null' => [
            [
                'color' => [
                    'backend_model' => 'adminhtml/system_config_backend_color',
                ],
            ],
            null,
        ];
        yield 'color' => [
            [
                'backend_model' => 'adminhtml/system_config_backend_color',
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
            'adminhtml/system_config_backend_color',
            'color',
        ];
        yield 'invalid' => [
            null,
            'invalid',
        ];
    }
}
