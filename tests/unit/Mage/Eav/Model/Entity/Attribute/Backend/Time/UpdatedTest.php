<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Eav\Model\Entity\Attribute\Backend\Time;

// use Mage;
// use Mage_Eav_Model_Entity_Attribute_Backend_Time_Updated as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Eav\Model\Entity\Attribute\Backend\Time\UpdatedTrait;

final class UpdatedTest extends OpenMageTest
{
    use UpdatedTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('eav/entity_attribute_backend_time_updated');
        self::markTestSkipped('');
    }
}
