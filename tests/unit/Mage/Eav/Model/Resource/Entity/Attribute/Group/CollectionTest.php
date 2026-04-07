<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Eav\Model\Resource\Entity\Attribute\Group;

# use Mage;
# use Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Eav\Model\Resource\Entity\Attribute\Group\CollectionTrait;

final class CollectionTest extends OpenMageTest
{
    use CollectionTrait;

    # private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        # self::$subject = Mage::getModel('eav/resource_entity_attribute_group_collection');
        self::markTestSkipped('');
    }
}
