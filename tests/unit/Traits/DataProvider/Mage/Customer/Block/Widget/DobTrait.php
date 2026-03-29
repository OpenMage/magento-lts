<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer\Block\Widget;

use Generator;

trait DobTrait
{
    public function provideGetYearData(): Generator
    {
        yield 'jan-1-2000' => ['2000', '2000-01-01'];
        yield 'jan-1-1999' => ['1999', '1999-01-01'];
        yield 'jan-1-2023' => ['2023', '2023-01-01'];
        yield 'mid-year' => ['2000', '2000-06-15'];
    }
}
