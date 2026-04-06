<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model\Entity\Address\Attribute\Source;

use Mage;
use Mage_Customer_Model_Entity_Address_Attribute_Source_Region as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer\Model\Entity\Address\Attribute\Source\RegionTrait;

final class RegionTest extends OpenMageTest
{
    use RegionTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('customer/entity_address_attribute_source_region');
    }
}
