<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
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
