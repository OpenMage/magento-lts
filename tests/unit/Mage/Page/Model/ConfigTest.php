<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Model;

use Mage;
use Mage_Page_Model_Config as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class ConfigTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('page/config');
    }

    /**
     * @group Model
     */
    public function testGetPageLayoutHandles(): void
    {
        static::assertIsArray(self::$subject->getPageLayoutHandles());
    }
}
