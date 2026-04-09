<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model\Customer\Attribute\Backend;

// use Mage;
// use Mage_Customer_Model_Customer_Attribute_Backend_Store as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer\Model\Customer\Attribute\Backend\StoreTrait;

final class StoreTest extends OpenMageTest
{
    use StoreTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('customer/customer_attribute_backend_store');
        self::markTestSkipped('');
    }
}
