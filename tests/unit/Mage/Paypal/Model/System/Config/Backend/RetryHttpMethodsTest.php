<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model\System\Config\Backend;

use Mage_Core_Exception;
use Mage_Core_Model_Config_Data;
use Mage_Paypal_Model_System_Config_Backend_RetryHttpMethods as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\System\Config\Backend\RetryHttpMethodsTrait;

final class RetryHttpMethodsTest extends OpenMageTest
{
    use RetryHttpMethodsTrait;

    /**
     * @dataProvider provideValidRetryHttpMethodsData
     */
    public function testBeforeSaveNormalizesValidMethods(string $expectedResult, mixed $value): void
    {
        $subject = new RetryHttpMethodsTestSubject();
        $subject->setData('value', $value);

        $subject->beforeSaveForTest();

        self::assertSame($expectedResult, $subject->getValue());
    }

    /**
     * @dataProvider provideInvalidRetryHttpMethodsData
     */
    public function testBeforeSaveRejectsInvalidMethods(mixed $value): void
    {
        $this->expectException(Mage_Core_Exception::class);

        $subject = new RetryHttpMethodsTestSubject();
        $subject->setData('value', $value);
        $subject->beforeSaveForTest();
    }
}

final class RetryHttpMethodsTestSubject extends Subject
{
    public function beforeSaveForTest(): Mage_Core_Model_Config_Data
    {
        return $this->_beforeSave();
    }
}
