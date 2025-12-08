<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

/**
 * PsrLogger model
 *
 * @package    Mage_Core
 */
class Mage_Core_Helper_PsrLogger extends Mage_Core_Helper_Abstract implements LoggerInterface
{
    use LoggerTrait;

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        Mage::log((string) $message, $level, null, false, $context);
    }
}
