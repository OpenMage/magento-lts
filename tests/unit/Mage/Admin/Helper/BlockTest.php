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

namespace OpenMage\Tests\Unit\Mage\Admin\Helper;

use Mage;
use Mage_Admin_Helper_Block as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class BlockTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('admin/block');
    }

    /**
     * @covers Mage_Admin_Helper_Block::isTypeAllowed()
     * @group Mage_Admin
     * @group Mage_Admin_Helper
     */
    public function testIsTypeAllowed(): void
    {
        static::assertFalse(self::$subject->isTypeAllowed('some-type'));
    }

    /**
     * @covers Mage_Admin_Helper_Block::getDisallowedBlockNames()
     * @group Mage_Admin
     * @group Mage_Admin_Helper
     */
    public function testGetDisallowedBlockNames(): void
    {
        static::assertSame(['install/end'], self::$subject->getDisallowedBlockNames());
    }
}
