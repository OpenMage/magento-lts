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
use Mage_Adminhtml_Helper_Addresses as Subject;

trait AddressTrait
{
    public function provideProcessStreetAttribute(): Generator
    {
        yield 'default' => [
            Subject::DEFAULT_STREET_LINES_COUNT,
            0,
        ];
        yield 'custom' => [
            4,
            4,
        ];
    }
}
