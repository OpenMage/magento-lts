<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Model\Resource;

use Mage;
use Mage_Admin_Model_Acl;
use Mage_Admin_Model_Acl_Resource;
use Mage_Admin_Model_Resource_Acl as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Admin\Model\Resource\AclTrait;

final class AclTest extends OpenMageTest
{
    use AclTrait;

    private const GROUP = 'G10';

    private const USER = 'U20';

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getResourceModel('admin/acl');
    }

    /**
     * A child with no rule row must not inherit the parent's allow.
     */
    public function testUnlistedChildOfAllowedParentIsDenied(): void
    {
        $acl = $this->buildAcl([
            $this->rule('admin/system', 'allow'),
            $this->rule('admin/system/config', 'deny'),
        ]);

        self::assertTrue($acl->isAllowed(self::GROUP, 'admin/system'));
        self::assertFalse($acl->isAllowed(self::GROUP, 'admin/system/config'));
        self::assertFalse($acl->isAllowed(self::GROUP, 'admin/system/newthing'));
    }

    public function testExplicitRowsAreUnchanged(): void
    {
        $acl = $this->buildAcl([
            $this->rule('admin/system', 'deny'),
            $this->rule('admin/system/config', 'allow'),
            $this->rule('admin/system/newthing', 'allow'),
        ]);

        self::assertFalse($acl->isAllowed(self::GROUP, 'admin/system'));
        self::assertTrue($acl->isAllowed(self::GROUP, 'admin/system/config'));
        self::assertTrue($acl->isAllowed(self::GROUP, 'admin/system/newthing'));
    }

    public function testAllRoleKeepsUnlistedResources(): void
    {
        $acl = $this->buildAcl([$this->rule('all', 'allow')]);

        self::assertTrue($acl->isAllowed(self::GROUP, 'admin/system/newthing'));
        self::assertTrue($acl->isAllowed(self::USER, 'admin/system/newthing'));
    }

    public function testUserRoleFollowsGroupDeny(): void
    {
        $acl = $this->buildAcl([$this->rule('admin/system', 'allow')]);

        self::assertTrue($acl->isAllowed(self::USER, 'admin/system'));
        self::assertFalse($acl->isAllowed(self::USER, 'admin/system/newthing'));
    }

    /**
     * The role editor writes an "all" deny row for every non-admin role. That row
     * must not count as full access.
     */
    public function testAllDenyRowDoesNotGrantUnlistedResources(): void
    {
        $acl = $this->buildAcl([
            $this->rule('all', 'deny'),
            $this->rule('admin/system', 'allow'),
        ]);

        self::assertTrue($acl->isAllowed(self::GROUP, 'admin/system'));
        self::assertFalse($acl->isAllowed(self::GROUP, 'admin/system/newthing'));
    }

    /**
     * @param array<int, array<string, mixed>> $rules
     */
    private function buildAcl(array $rules): Mage_Admin_Model_Acl
    {
        $acl = new Mage_Admin_Model_Acl();
        // loadAclResources() registers "all" as a resource of its own.
        $acl->addResource(new Mage_Admin_Model_Acl_Resource('all'));
        $acl->addResource(new Mage_Admin_Model_Acl_Resource('admin'));
        $acl->addResource(new Mage_Admin_Model_Acl_Resource('admin/system'), 'admin');
        $acl->addResource(new Mage_Admin_Model_Acl_Resource('admin/system/config'), 'admin/system');
        $acl->addResource(new Mage_Admin_Model_Acl_Resource('admin/system/newthing'), 'admin/system');

        self::$subject->loadRoles($acl, [
            ['role_id' => 10, 'parent_id' => 0, 'role_type' => 'G', 'user_id' => 0],
            ['role_id' => 11, 'parent_id' => 10, 'role_type' => 'U', 'user_id' => 20],
        ]);
        self::$subject->loadRules($acl, $rules);
        self::$subject->denyUnlistedResources($acl, $rules);

        return $acl;
    }

    /**
     * @return array<string, mixed>
     */
    private function rule(string $resource, string $permission): array
    {
        return [
            'role_id' => 10,
            'role_type' => 'G',
            'resource_id' => $resource,
            'privileges' => '',
            'assert_id' => 0,
            'permission' => $permission,
        ];
    }
}
