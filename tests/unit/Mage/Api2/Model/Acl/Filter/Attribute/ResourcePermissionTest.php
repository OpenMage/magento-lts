<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Api2\Model\Acl\Filter\Attribute;

// use Mage;
// use Mage_Api2_Model_Acl_Filter_Attribute_ResourcePermission as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Api2\Model\Acl\Filter\Attribute\ResourcePermissionTrait;

final class ResourcePermissionTest extends OpenMageTest
{
    use ResourcePermissionTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('api2/acl_filter_attribute_resourcepermission');
        self::markTestSkipped('');
    }
}
