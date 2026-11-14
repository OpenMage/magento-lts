<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model;

use Mage_Sales_Model_Order;
use Mage_SalesRule_Model_Observer;

/**
 * Exposes the per request bookkeeping of quotes whose orders were already counted
 */
final class ObserverTestFixture extends Mage_SalesRule_Model_Observer
{
    public function isQuoteCounted(Mage_Sales_Model_Order $order): bool
    {
        return $this->_isQuoteCounted($order);
    }

    public function setQuoteCounted(Mage_Sales_Model_Order $order): void
    {
        $this->_setQuoteCounted($order);
    }
}
