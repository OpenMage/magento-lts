<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\Design;

use Generator;

trait FallbackTrait
{
    /**
     * @return Generator<string, array{array<int, array<string, string>>, string, string, string}, mixed, void>
     */
    public static function provideGetFallbackSchemeData(): Generator
    {
        $openmageDefault = [
            [],
            ['_package' => 'openmage', '_theme' => 'default'],
            ['_package' => 'default', '_theme' => 'default'],
            ['_package' => 'base', '_theme' => 'default'],
        ];

        yield 'existing theme' => [
            [[], ['_package' => 'default', '_theme' => 'default'], ['_package' => 'base', '_theme' => 'default']],
            'default',
            'openmage',
            'default',
        ];
        yield 'missing theme, configured theme exists' => [$openmageDefault, 'default', 'openmage', 'custom'];
        yield 'missing theme is the configured theme' => [$openmageDefault, 'custom', 'openmage', 'custom'];
    }
}
