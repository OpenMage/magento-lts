<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Override;
use Mage;
use Mage_Core_Model_Logger as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\LoggerTrait;

final class LoggerTest extends OpenMageTest
{
    use LoggerTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/logger');
    }

    /**
     * @dataProvider provideLogData
     * @group Model
     */
    public function testLog($message, $level, $file, $forceLog, $context)
    {
        if (!in_array($file, ['php://stdout', 'php://stderr'], true)) {
            self::$subject->log($message, $level, $file, $forceLog, $context);

            $logDir = Mage::getBaseDir('var') . DS . 'log' . DS;
            $file = $logDir . $file;

            self::assertFileExists($file, 'Log file does not exist.');

            if (file_exists($file)) {
                unlink($file);
            }

            self::assertFileDoesNotExist($file, 'Log file was not deleted.');
        }
    }
}
