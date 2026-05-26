<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

use Carbon\FactoryImmutable;
use Psr\Clock\ClockInterface;

/**
 * Core clock helper
 *
 * @package    Mage_Core
 */
class Mage_Core_Helper_Clock extends Mage_Core_Helper_Abstract implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        static $clock = new FactoryImmutable();
        return $clock->now();
    }

    public function getTimestamp(): int
    {
        return $this->now()->getTimestamp();
    }

    public function format(string $format): string
    {
        return $this->now()->format($format);
    }
}
