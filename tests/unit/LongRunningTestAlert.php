<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit;

use PHPUnit\Runner\AfterTestHook;

/**
 * Class LongRunningTestAlert
 */
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
