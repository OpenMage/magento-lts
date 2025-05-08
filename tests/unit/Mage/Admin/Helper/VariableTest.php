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
use Mage_Admin_Helper_Variable as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class VariableTest extends OpenMageTest
{
    public static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('admin/variable');
    }

    /**
     * @covers Mage_Admin_Helper_Variable::isPathAllowed()
     * @group Helper
     */
    public function testIsPathAllowed(): void
    {
        static::assertIsBool(self::$subject->isPathAllowed(''));
    }
}
