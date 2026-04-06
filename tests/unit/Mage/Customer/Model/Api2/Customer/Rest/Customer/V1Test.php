<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model\Api2\Customer\Rest\Customer;

use Mage;
use Mage_Customer_Model_Api2_Customer_Rest_Customer_V1 as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer\Model\Api2\Customer\Rest\Customer\V1Trait;

final class V1Test extends OpenMageTest
{
    use V1Trait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('customer/api2_customer_rest_customer_v1');
        self::markTestSkipped('');
    }
}
