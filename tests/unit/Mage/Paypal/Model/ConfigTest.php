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
        $this->setPaypalConfig('api_timeout', $value);

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
            $this->setPaypalConfig($field, $value);
        }

        self::assertSame($expectedResult, self::$subject->getRetryConfiguration());
    }

    /**
     * @dataProvider provideButtonTypeData
     * @group Model
     */
    public function testGetButtonType(string $expectedResult, string $label): void
    {
        $this->setPaypalConfig('button_label', $label);

        self::assertSame($expectedResult, self::$subject->getButtonType());
    }

    /**
     * @group Model
     */
    public function testGetSdkConfig(): void
    {
        $this->setPaypalConfig('sandbox_mode', '1');
        $this->setPaypalConfig('debug', '0');
        $this->setPaypalConfig('button_label', 'pay');
        $this->setPaypalConfig('show_paylater_button', '1');
        $this->setPaypalConfig('show_credit_button', '0');

        $config = self::$subject->getSdkConfig(Subject::PAGE_TYPE_CART, 'USD', 42.5);

        self::assertSame('https://www.sandbox.paypal.com/web-sdk/v6/core', $config['sdkUrl']);
        self::assertSame(Subject::PAGE_TYPE_CART, $config['pageType']);
        self::assertSame('USD', $config['currencyCode']);
        self::assertSame('42.50', $config['amount']);
        self::assertSame('pay', $config['buttonType']);
        self::assertTrue($config['payLaterEnabled']);
        self::assertFalse($config['creditEnabled']);
        self::assertStringNotContainsString('_', $config['locale']);
    }

    /**
     * @group Model
     */
    public function testGetSdkConfigLetsTheCallerChooseTheButtonType(): void
    {
        $this->setPaypalConfig('button_label', 'pay');

        $config = self::$subject->getSdkConfig(Subject::PAGE_TYPE_PRODUCT, 'EUR', null, Subject::BUTTON_LABEL_BUYNOW);

        self::assertSame(Subject::BUTTON_LABEL_BUYNOW, $config['buttonType']);
        self::assertSame('', $config['amount']);
    }

    private function setPaypalConfig(string $field, string $value): void
    {
        Mage::app()->getStore()->setConfig('payment/paypal/' . $field, $value);
    }
}
