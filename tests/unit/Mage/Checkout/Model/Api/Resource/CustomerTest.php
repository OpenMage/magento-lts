<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Checkout\Model\Api\Resource;

// use Mage;
// use Mage_Checkout_Model_Api_Resource_Customer as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Checkout\Model\Api\Resource\CustomerTrait;

final class CustomerTest extends OpenMageTest
{
    use CustomerTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('checkout/api_resource_customer');
        self::markTestSkipped('');
    }
}
