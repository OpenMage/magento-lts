<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Mage;
use Mage_Core_Helper_Hint as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class HintTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('core/hint');
    }

    /**
     * @covers Mage_Core_Helper_Hint::getAvailableHints()
     * @group Helper
     */
    public function testGetAvailableHints(): void
    {
        static::assertSame([], self::$subject->getAvailableHints());
    }

    /**
     * @covers Mage_Core_Helper_Hint::getHintByCode()
     * @group Helper
     */
    public function testGetHintByCode(): void
    {
        static::assertNull(self::$subject->getHintByCode('test'));
    }
}
