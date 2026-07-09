<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit;

use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

/**
 * Class LongRunningTestAlert
 */
class LongRunningTestAlert implements Extension, FinishedSubscriber
{
    protected const MAX_SECONDS_ALLOWED = 1.0;

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscriber($this);
    }

    public function notify(Finished $event): void
    {
        $test = $event->test()->name();
        $time = $event->telemetryInfo()->durationSinceStart()->asFloat();
        if ($time > self::MAX_SECONDS_ALLOWED) {
            file_put_contents('php://stderr', sprintf("\n\nThe %s test took %s seconds!\n\n", $test, $time));
        }
    }
}
