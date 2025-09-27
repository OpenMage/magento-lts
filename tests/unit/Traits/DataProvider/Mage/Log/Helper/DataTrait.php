<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Log\Helper;

use Generator;

trait DataTrait
{
    public function provideIsLogFileExtensionValid(): Generator
    {
        yield 'valid' => [
            true,
            'valid.log',
        ];
        yield 'invalid' => [
            false,
            'invalid.file',
        ];
    }
}
