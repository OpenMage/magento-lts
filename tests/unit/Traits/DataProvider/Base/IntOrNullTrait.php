<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Base;

use Generator;

trait IntOrNullTrait
{
    public function provideIntOrNull(): Generator
    {
        yield 'null' => [
            null,
        ];
        yield 'expexted int' => [
            1,
        ];
        yield 'not expexted int' => [
            99999,
        ];
    }
}
