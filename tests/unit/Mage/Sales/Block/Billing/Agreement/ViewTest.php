<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Block\Billing\Agreement;

use Mage;
use Mage_Core_Model_Session;
use Mage_Sales_Block_Billing_Agreement_View as Subject;
use Mage_Sales_Model_Billing_Agreement;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Block\Billing\Agreement\ViewTrait;
use Override;
use ReflectionMethod;
use ReflectionProperty;

final class ViewTest extends OpenMageTest
{
    use ViewTrait;

    private const FORM_KEY = 'TestFormKey123';

    #[Override]
    protected function setUp(): void
    {
        $coreSession = $this->createPartialMock(Mage_Core_Model_Session::class, ['getFormKey']);
        $coreSession->method('getFormKey')->willReturn(self::FORM_KEY);
        Mage::unregister('_singleton/core/session');
        Mage::register('_singleton/core/session', $coreSession);
    }

    #[Override]
    protected function tearDown(): void
    {
        Mage::unregister('_singleton/core/session');
    }

    /**
     * The cancel URL must carry the session form key, the controller rejects requests without
     * it (GHSA-9783-jfj5-gfpv). Themes still navigating to it with a GET rely on it.
     *
     * @group Block
     */
    public function testCancelUrlCarriesFormKey(): void
    {
        $agreement = new Mage_Sales_Model_Billing_Agreement();
        $agreement->setId(3)->setMethodCode('paypal_billing_agreement');

        $subject = new Subject();
        $subject->setTemplate('');
        (new ReflectionProperty(Subject::class, '_billingAgreementInstance'))->setValue($subject, $agreement);
        (new ReflectionMethod(Subject::class, '_toHtml'))->invoke($subject);

        $result = $subject->getCancelUrl();
        self::assertIsString($result);
        // '*/billing_agreement/cancel' resolves against the current request, which a unit test does not have
        self::assertStringContainsString('/payment_method/paypal_billing_agreement/', $result);
        self::assertStringContainsString('/form_key/' . self::FORM_KEY . '/', $result);
    }
}
