<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model;

use Generator;

trait LoggerTrait
{
    public function provideLogData(): Generator
    {
        yield 'file' => [
            'Test message',
            null,
            'test-phpunit.log',
            true,
            [],
        ];
        yield 'stdout' => [
            'Test stdout message',
            null,
            'php://stdout',
            true,
            [],
        ];
        yield 'stderr' => [
            'Test stderr message',
            null,
            'php://stderr',
            true,
            [],
        ];
    }
}
