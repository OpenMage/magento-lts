<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Model\Resource\Report\Refunded\Collection;

use Mage;
use Mage_Sales_Model_Resource_Report_Refunded_Collection_Order as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Resource\Report\Refunded\Collection\OrderTrait;

final class OrderTest extends OpenMageTest
{
    use OrderTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('sales/resource_report_refunded_collection_order');
        self::markTestSkipped('');
    }
}
