<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Widget\Model\Resource\Widget;

use Generator;

trait InstanceTrait
{
    /**
     * @return Generator<string, array{int, bool}>
     */
    public static function provideCleanupBatchData(): Generator
    {
        yield 'one layout update per batch' => [1, true];
        yield 'two layout updates per batch' => [2, true];
        yield 'all in one batch' => [200, true];
        // As in setup scripts, which turn foreign key checks off, so ON DELETE CASCADE does not run
        yield 'foreign key checks off' => [1, false];
    }
}
