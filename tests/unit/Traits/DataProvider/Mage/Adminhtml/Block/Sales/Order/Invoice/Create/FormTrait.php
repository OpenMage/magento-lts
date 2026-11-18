<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Block\Sales\Order\Invoice\Create;

use Generator;

trait FormTrait
{
    /**
     * Ship permission, order ships with its invoice, quantity left to ship, expected result.
     *
     * @return Generator<string, array<mixed>>
     */
    public static function provideCanCreateShipmentData(): Generator
    {
        yield 'offered with ship' => [true, false, 2.0, true];
        yield 'hidden without ship' => [false, false, 2.0, false];
        yield 'shown without ship when the order ships with its invoice' => [false, true, 2.0, true];
        yield 'hidden when nothing is left to ship' => [true, false, 0.0, false];
    }
}
