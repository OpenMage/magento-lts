<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model\Resource\Address\Attribute\Source;

use Mage;
use Mage_Customer_Model_Resource_Address_Attribute_Source_Region as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class RegionTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('customer/resource_address_attribute_source_region');
    }
}
