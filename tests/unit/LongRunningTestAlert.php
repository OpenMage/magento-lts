<?php

/**
 * Class LongRunningTestAlert
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit;

use PHPUnit\Runner\AfterTestHook;

class LongRunningTestAlert implements AfterTestHook
{
    protected const MAX_SECONDS_ALLOWED = 1.0;

    public function executeAfterTest(string $test, float $time): void
    {
        if ($time > self::MAX_SECONDS_ALLOWED) {
            file_put_contents('php://stderr', sprintf("\n\nThe %s test took %s seconds!\n\n", $test, $time));
        }
    }
}
