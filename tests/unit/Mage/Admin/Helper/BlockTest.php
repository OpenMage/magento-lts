<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Admin\Helper;

use Mage;
use Mage_Admin_Helper_Block as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class BlockTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('admin/block');
    }

    /**
     * @covers Mage_Admin_Helper_Block::isTypeAllowed()
     * @group Helper
     */
    public function testIsTypeAllowed(): void
    {
        static::assertFalse(self::$subject->isTypeAllowed('some-type'));
    }

    /**
     * @covers Mage_Admin_Helper_Block::getDisallowedBlockNames()
     * @group Helper
     */
    public function testGetDisallowedBlockNames(): void
    {
        static::assertSame(['install/end'], self::$subject->getDisallowedBlockNames());
    }
}
