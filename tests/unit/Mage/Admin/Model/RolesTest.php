<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Model;

use Mage;
use Mage_Admin_Model_Roles as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class RolesTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('admin/roles');
    }

    /**
     * @covers Mage_Admin_Model_Resource_Roles::getRoleUsers()
     * @covers Mage_Admin_Model_Roles::getRoleUsers()
     * @group Model
     */
    public function testGetRoleUsers(): void
    {
        self::assertIsArray(self::$subject->getRoleUsers());
    }
}
