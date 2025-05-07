<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Tax;

use Generator;

trait TaxTrait
{
    public function provideGetIncExcText(): Generator
    {
        yield 'true' => [
            'Incl. Tax',
            true,
        ];
        yield 'false' => [
            'Excl. Tax',
            false,
        ];
    }

    public function provideGetIncExcTaxLabel(): Generator
    {
        yield 'true' => [
            '(Incl. Tax)',
            true,
        ];
        yield 'false' => [
            '(Excl. Tax)',
            false,
        ];
    }
}
