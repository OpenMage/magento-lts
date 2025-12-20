<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

/**
 * Provides a PSR-3 LoggerInterface implementation that wraps Mage::log().
 *
 * This class enables integration with PSR-3 compatible logging libraries and tools
 * by forwarding log messages to the native Mage::log() method. It allows Magento
 * modules and external libraries to use standardized logging practices within the
 * OpenMage framework.
 *
 * @package    Mage_Core
 */
class Mage_Core_Helper_PsrLogger extends Mage_Core_Helper_Abstract implements LoggerInterface
{
    use LoggerTrait;

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        // unknown log level need to throw an InvalidArgumentException
        $reflectionClass = new ReflectionClass(LogLevel::class);
        if (!in_array($level, $reflectionClass->getConstants())) {
            throw new InvalidArgumentException('Level "' . $level . '" is not defined, use one of: ' . implode(', ', $reflectionClass->getConstants()));
        }

        Mage::log((string) $message, $level, null, false, $context);
    }
}
