<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Model;

use Mage;
use Mage_Admin_Model_Acl;
use Mage_Admin_Model_Config as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Varien_Simplexml_Config;

class ConfigTest extends OpenMageTest
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
        static::assertInstanceOf(self::$subject::class, self::$subject->loadAclResources(new Mage_Admin_Model_Acl()));
    }

    /**
     * @group Model
     */
    public function testGetAdminhtmlConfig(): void
    {
        static::assertInstanceOf(Varien_Simplexml_Config::class, self::$subject->getAdminhtmlConfig());
    }
}
