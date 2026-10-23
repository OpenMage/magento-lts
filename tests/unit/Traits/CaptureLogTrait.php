<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits;

use Mage;
use OpenMage\Tests\Unit\Mage\Core\Model\LoggerTestHelper;

/**
 * Replaces the core/logger singleton, so Mage::log() calls can be inspected
 *
 * Call restoreLogger() in tearDown().
 */
trait CaptureLogTrait
{
    private ?object $originalLogger = null;

    private bool $logCaptured = false;

    private function captureLog(): LoggerTestHelper
    {
        $logger = new LoggerTestHelper();

        $this->originalLogger = Mage::registry('_singleton/core/logger');
        Mage::unregister('_singleton/core/logger');
        Mage::register('_singleton/core/logger', $logger);
        $this->logCaptured = true;

        return $logger;
    }

    private function restoreLogger(): void
    {
        if (!$this->logCaptured) {
            return;
        }

        Mage::unregister('_singleton/core/logger');
        if ($this->originalLogger !== null) {
            Mage::register('_singleton/core/logger', $this->originalLogger);
        }

        $this->originalLogger = null;
        $this->logCaptured = false;
    }
}
