<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Log\Model;

use Generator;

trait CustomerTrait
{
    public function provideGetLoginAtTimestampData(): Generator
    {
        yield 'valid' => [
            true,
            [
                'getLoginAt' => true,
            ],
        ];
        yield 'invalid' => [
            false,
            [
                'getLoginAt' => false,
            ],
        ];
    }
}
