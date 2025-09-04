<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Uploader\Helper;

use Mage;
use Mage_Uploader_Helper_Data as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class DataTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('uploader/data');
    }

    /**
     * @group Helper
     */
    public function testIsModuleEnabled(): void
    {
        static::assertIsBool(self::$subject->isModuleEnabled());
    }
}
