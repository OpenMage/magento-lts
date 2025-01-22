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

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model;

use Generator;

trait LocaleTrait
{
    public function provideGetNumberData(): Generator
    {
        yield 'array' => [
            1.0,
            [1],
        ];
        yield 'int' => [
            1.0,
            1,
        ];
        yield 'string' => [
            1.0,
            '1',
        ];
        yield 'string comma' => [
            1.0,
            '1,0',
        ];
        yield 'string dot' => [
            1.0,
            '1.0',
        ];
        yield 'null' => [
            null,
            null,
        ];
    }
}
