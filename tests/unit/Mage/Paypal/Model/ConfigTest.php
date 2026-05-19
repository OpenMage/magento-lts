<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Model;

use Mage;
use Mage_Paypal_Model_Config as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\ConfigTrait;

final class ConfigTest extends OpenMageTest
{
    use ConfigTrait;

    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('paypal/config');
    }

    /**
     * @dataProvider provideApiTimeoutData
     */
    public function testGetApiTimeout(int $expectedResult, string $value): void
    {
        self::setPaypalConfig('api_timeout', $value);

        self::assertSame($expectedResult, self::$subject->getApiTimeout());
    }

    /**
     * @dataProvider provideGetRetryConfigurationData
     * @param array{
     *     enabled: bool,
     *     number_of_retries: int,
     *     retry_interval: float,
     *     backoff_factor: float,
     *     maximum_retry_wait_time: int,
     *     retry_on_timeout: bool,
     *     http_status_codes: int[],
     *     http_methods: string[]
     * } $expectedResult
     * @param array<string, string> $configValues
     */
    public function testGetRetryConfiguration(array $expectedResult, array $configValues): void
    {
        foreach ($configValues as $field => $value) {
            self::setPaypalConfig($field, $value);
        }

        self::assertSame($expectedResult, self::$subject->getRetryConfiguration());
    }

    private static function setPaypalConfig(string $field, string $value): void
    {
        Mage::app()->getStore()->setConfig('payment/paypal/' . $field, $value);
    }
}
