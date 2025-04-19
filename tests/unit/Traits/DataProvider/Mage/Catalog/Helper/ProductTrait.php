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

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Helper;

use Generator;

trait ProductTrait
{
    public function provideGetAttributeInputTypes(): Generator
    {
        yield 'null' => [
            2,
            null,
        ];
        yield 'invalid' => [
            0,
            'invalid',
        ];
        yield 'multiselect' => [
            1,
            'multiselect',
        ];
        yield 'boolean' => [
            1,
            'boolean',
        ];
    }

    public function provideGetAttributeBackendModelByInputType(): Generator
    {
        yield 'multiselect' => [
            'eav/entity_attribute_backend_array',
            'multiselect',
        ];
    }

    public function provideGetAttributeSourceModelByInputType(): Generator
    {
        yield 'boolean' => [
            'eav/entity_attribute_source_boolean',
            'boolean',
        ];
    }
}
