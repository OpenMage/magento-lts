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
use Mage_Admin_Model_Acl;
use Mage_Admin_Model_Config as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Simplexml_Config;

final class ConfigTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('admin/config');
    }

    /**
     * @group Model
     */
    public function testGetAclAssert(): void
    {
        static::assertFalse(self::$subject->getAclAssert(''));
    }

    /**
     * @group Model
     */
    public function testGetAclPrivilegeSet(): void
    {
        static::assertFalse(self::$subject->getAclPrivilegeSet());
    }

    /**
     * @group Model
     */
    public function testLoadAclResources(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->loadAclResources(new Mage_Admin_Model_Acl()));
    }

    /**
     * @group Model
     */
    public function testGetAdminhtmlConfig(): void
    {
        static::assertInstanceOf(Varien_Simplexml_Config::class, self::$subject->getAdminhtmlConfig());
    }
}
