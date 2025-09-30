<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Config as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class ConfigTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/config');
    }

    /**
     * @group Model
     */
    public function testSaveDeleteGetConfig(): void
    {
        $path = 'test/config';
        $value = 'foo';

        self::assertFalse(self::$subject->getConfig($path));

        self::$subject->saveConfig($path, $value);
        self::assertSame($value, self::$subject->getConfig($path));

        self::$subject->deleteConfig($path);
        self::assertFalse(self::$subject->getConfig($path));
    }
}
