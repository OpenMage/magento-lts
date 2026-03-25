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
        // Jan 1, 2000 is in ISO week 52 of 1999 - must return 2000, not 1999
        yield 'jan-1-2000' => ['2000', '2000-01-01'];
        // Jan 1, 1999 is in ISO week 53 of 1998 - must return 1999, not 1998
        yield 'jan-1-1999' => ['1999', '1999-01-01'];
        // Jan 1, 2023 is in ISO week 52 of 2022 - must return 2023, not 2022
        yield 'jan-1-2023' => ['2023', '2023-01-01'];
        // A mid-year date - no ambiguity
        yield 'mid-year' => ['2000', '2000-06-15'];
    }
}
