<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Api2\Model\Resource\Acl\Global\Role;

use Mage;
use Mage_Api2_Model_Resource_Acl_Global_Role_Collection as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Api2\Model\Resource\Acl\Global\Role\CollectionTrait;

final class CollectionTest extends OpenMageTest
{
    use CollectionTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('api2/resource_acl_global_role_collection');
    }
}
