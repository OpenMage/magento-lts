<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Block\Adminhtml\Settlement;

// use Mage_Paypal_Block_Adminhtml_Settlement_Report as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Block\Adminhtml\Settlement\ReportTrait;

final class ReportTest extends OpenMageTest
{
    use ReportTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = new Subject();
        self::markTestSkipped('');
    }
}
