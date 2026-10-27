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
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\System\Config\Backend\RetryStatusCodesTrait;

final class RetryStatusCodesTest extends OpenMageTest
{
    use RetryStatusCodesTrait;

    /**
     * @dataProvider provideValidRetryStatusCodesData
     */
    public function testBeforeSaveNormalizesValidStatusCodes(string $expectedResult, mixed $value): void
    {
        $subject = new RetryStatusCodesTestSubject();
        $subject->setData('value', $value);

        $subject->beforeSaveForTest();

        self::assertSame($expectedResult, $subject->getValue());
    }

    /**
     * @dataProvider provideInvalidRetryStatusCodesData
     */
    public function testBeforeSaveRejectsInvalidStatusCodes(mixed $value): void
    {
        $this->expectException(Mage_Core_Exception::class);

        $subject = new RetryStatusCodesTestSubject();
        $subject->setData('value', $value);
        $subject->beforeSaveForTest();
    }
}
