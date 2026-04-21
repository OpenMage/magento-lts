<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Block\Adminhtml\Billing\Agreement\View\Tab;

// use Mage_Sales_Block_Adminhtml_Billing_Agreement_View_Tab_Orders as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Block\Adminhtml\Billing\Agreement\View\Tab\OrdersTrait;

final class OrdersTest extends OpenMageTest
{
    use OrdersTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = new Subject();
        self::markTestSkipped('');
    }
}
