<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Shipping\Model\Tracking\Result;

use Mage;
use Mage_Shipping_Model_Tracking_Result_Status as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class StatusTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('shipping/tracking_result_status');
    }
}
