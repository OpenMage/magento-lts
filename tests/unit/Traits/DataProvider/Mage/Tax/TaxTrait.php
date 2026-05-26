<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Tax;

use Generator;

trait TaxTrait
{
    public static function provideGetIncExcText(): Generator
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

    public static function provideGetIncExcTaxLabel(): Generator
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
