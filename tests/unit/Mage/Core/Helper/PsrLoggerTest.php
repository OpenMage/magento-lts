<?php


/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Helper;

use Override;
use Mage;
use Mage_Core_Exception;
use Mage_Core_Model_Logger;
use Mage_Core_Helper_PsrLogger as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper\PsrLoggerTrait;

final class PsrLoggerTest extends OpenMageTest
{
    use PsrLoggerTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('core/psrLogger');
    }


    /**
     * @dataProvider provideLogData
     * @group Helper
     * @throws Mage_Core_Exception
     */
    public function testLog($level, $message, $context)
    {
        $logger = $this->createMock(Mage_Core_Model_Logger::class);
        $logger->expects($this->once())->method('log')->with($message, $level, '', false, $context);
        $registryKey = '_singleton/core/logger';
        Mage::unregister($registryKey);
        Mage::register($registryKey, $logger);

        self::$subject->log($level, $message, $context);
    }
}