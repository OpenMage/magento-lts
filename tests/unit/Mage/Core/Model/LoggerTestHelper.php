<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage_Core_Model_Logger;

/**
 * Collects the logged [message, level] pairs instead of writing them
 */
class LoggerTestHelper extends Mage_Core_Model_Logger
{
    /** @var list<array{0: mixed, 1: mixed}> */
    public array $messages = [];

    /**
     * @param array<mixed> $context
     */
    public function log($message, $level = null, $file = '', $forceLog = false, array $context = []): void
    {
        $this->messages[] = [$message, $level];
    }
}
