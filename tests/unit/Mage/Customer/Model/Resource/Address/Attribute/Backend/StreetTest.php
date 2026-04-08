<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model\Resource\Address\Attribute\Backend;

// use Mage;
// use Mage_Customer_Model_Resource_Address_Attribute_Backend_Street as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer\Model\Resource\Address\Attribute\Backend\StreetTrait;

final class StreetTest extends OpenMageTest
{
    use StreetTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('customer/resource_address_attribute_backend_street');
        self::markTestSkipped('');
    }
}
