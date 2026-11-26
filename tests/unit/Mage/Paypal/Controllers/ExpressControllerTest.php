<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Paypal\Controllers;

use Iterator;
use Error;
use Mage;
use Mage_Checkout_Helper_Data;
use Mage_Core_Controller_Request_Http;
use Mage_Core_Controller_Response_Http;
use Mage_Core_Model_Message;
use Mage_Core_Model_Session_Abstract;
use Mage_Paypal_ExpressController as Subject;
use Mage_Paypal_Model_Express_Checkout;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * GHSA-5xfj-mgf7-27gm: Express Checkout actions that change the quote or place the order
 * must not be reachable by a cross-site request.
 *
 * _initCheckout() is the first step after each guard; the subject's version throws an
 * Error (not caught by the actions) so that passing the guard is observable without a quote.
 */
final class ExpressControllerTest extends OpenMageTest
{
    private const INVALID_FORM_KEY = 'Invalid form key. Please refresh the page.';

    private const WRONG_TOKEN = 'Wrong PayPal Express Checkout Token specified.';

    private const PASSED_GUARD = 'passed guard';

    private const CHECKOUT_HELPER_KEY = '_helper/checkout';

    private const VERIFIED_RETURN_KEY = 'paypal_express_verified_return';

    private const SESSION_TOKEN = 'EC-SESSION';

    protected function setUp(): void
    {
        parent::setUp();
        // Zend_Controller_Request_Http::setQuery()/setPost() write to the superglobals
        $_GET = [];
        $_POST = [];
        Mage::app()->getStore()->setConfig('system/csrf/use_form_key', '1');
        Mage::getSingleton('paypal/session')->getMessages(true);
        Mage::getSingleton('checkout/session')->getMessages(true);
        Mage::getSingleton('paypal/session')->unsExpressCheckoutToken();
        Mage::getSingleton('checkout/session')->unsPaypalTransactionData();
        Mage::unregister(self::VERIFIED_RETURN_KEY);

        $checkoutHelper = $this->createMock(Mage_Checkout_Helper_Data::class);
        $checkoutHelper->method('getRequiredAgreementIds')->willReturn([]);
        Mage::unregister(self::CHECKOUT_HELPER_KEY);
        Mage::register(self::CHECKOUT_HELPER_KEY, $checkoutHelper);
    }

    protected function tearDown(): void
    {
        Mage::unregister(self::CHECKOUT_HELPER_KEY);
        Mage::unregister(self::VERIFIED_RETURN_KEY);
        Mage::getSingleton('checkout/session')->unsPaypalTransactionData();
        $_GET = [];
        $_POST = [];
        parent::tearDown();
    }

    /**
     * @return Iterator<string, array{string, (null|string), bool}>
     */
    public static function providePlaceOrderRequests(): Iterator
    {
        yield 'GET without form key' => ['GET', null, false];
        yield 'GET with valid form key' => ['GET', 'valid', false];
        yield 'POST without form key' => ['POST', null, false];
        yield 'POST with wrong form key' => ['POST', 'wrong', false];
        yield 'POST with valid form key' => ['POST', 'valid', true];
    }

    /**
     * @dataProvider providePlaceOrderRequests
     * @group Controller
     */
    public function testPlaceOrderRequiresPostAndFormKey(string $method, ?string $formKey, bool $allowed): void
    {
        $request = $this->createRequest('placeOrder', $method, $formKey);
        $subject = $this->createSubject($request, $allowed);

        $subject->placeOrderAction();

        self::assertSame([self::INVALID_FORM_KEY], $this->getErrors(Mage::getSingleton('paypal/session')));
        self::assertTrue($subject->getResponse()->isRedirect());
    }

    /**
     * @group Controller
     */
    public function testPlaceOrderForwardedFromCheckedReturnSkipsFormKey(): void
    {
        Mage::getSingleton('paypal/session')->setExpressCheckoutToken(self::SESSION_TOKEN);
        $request = $this->createRequest('return', 'GET', null);
        $request->setQuery('token', self::SESSION_TOKEN);

        $checkout = $this->createMock(Mage_Paypal_Model_Express_Checkout::class);
        $checkout->expects(self::once())->method('returnFromPaypal')->with(self::SESSION_TOKEN);
        $checkout->method('canSkipOrderReviewStep')->willReturn(true);
        $subject = $this->getMockBuilder(Subject::class)
            ->setConstructorArgs([$request, new Mage_Core_Controller_Response_Http()])
            ->onlyMethods(['_initCheckout'])
            ->getMock();
        $subject->method('_initCheckout')->willReturn($checkout);

        $subject->returnAction();

        self::assertSame('placeOrder', $request->getActionName());
        self::assertFalse($request->isDispatched());
        $this->createSubject($request, true)->placeOrderAction();
    }

    /**
     * @group Controller
     */
    public function testPlaceOrderForwardedWithoutCheckedReturnRequiresFormKey(): void
    {
        $request = $this->createRequest('return', 'GET', null);
        $request->initForward();
        $request->setActionName('placeOrder');

        $this->createSubject($request, false)->placeOrderAction();

        self::assertSame([self::INVALID_FORM_KEY], $this->getErrors(Mage::getSingleton('paypal/session')));
    }

    /**
     * @group Controller
     */
    public function testCheckedReturnWaivesTheFormKeyOnce(): void
    {
        $request = $this->createRequest('return', 'GET', null);
        $request->setQuery('retry_authorization', 'true');
        $request->setQuery('token', self::SESSION_TOKEN);
        Mage::getSingleton('paypal/session')->setExpressCheckoutToken(self::SESSION_TOKEN);
        Mage::getSingleton('checkout/session')->setPaypalTransactionData(['transaction_id' => 'O-1']);
        $this->createSubject($request, false)->returnAction();
        $forwarded = $this->getMockBuilder(Subject::class)
            ->setConstructorArgs([$request, new Mage_Core_Controller_Response_Http()])
            ->onlyMethods(['_initCheckout'])
            ->getMock();
        $forwarded->expects(self::once())->method('_initCheckout')->willThrowException(new Error(self::PASSED_GUARD));
        try {
            $forwarded->placeOrderAction();
            self::fail('The forwarded return did not reach the checkout');
        } catch (Error $error) {
            self::assertSame(self::PASSED_GUARD, $error->getMessage());
        }

        $this->createSubject($request, false)->placeOrderAction();

        self::assertSame([self::INVALID_FORM_KEY], $this->getErrors(Mage::getSingleton('paypal/session')));
    }

    /**
     * @return Iterator<string, array{(null|string), bool}>
     */
    public static function provideRetryAuthorizationRequests(): Iterator
    {
        yield 'no request token' => [null, false];
        yield 'wrong request token' => ['EC-OTHER', false];
        yield 'matching request token' => [self::SESSION_TOKEN, true];
    }

    /**
     * @dataProvider provideRetryAuthorizationRequests
     * @group Controller
     */
    public function testRetryAuthorizationRequiresTheExpressCheckoutToken(?string $requestToken, bool $forwarded): void
    {
        Mage::getSingleton('paypal/session')->setExpressCheckoutToken(self::SESSION_TOKEN);
        Mage::getSingleton('checkout/session')->setPaypalTransactionData(['transaction_id' => 'O-1']);
        $request = $this->createRequest('return', 'GET', null);
        $request->setQuery('retry_authorization', 'true');
        if ($requestToken !== null) {
            $request->setQuery('token', $requestToken);
        }

        $subject = $this->createSubject($request, false);
        $subject->returnAction();

        if ($forwarded) {
            self::assertSame('placeOrder', $request->getActionName());
            $this->createSubject($request, true)->placeOrderAction();
            return;
        }

        self::assertSame('return', $request->getActionName());
        self::assertTrue($subject->getResponse()->isRedirect());
        $this->createSubject($request, false)->placeOrderAction();
        self::assertSame([self::INVALID_FORM_KEY], $this->getErrors(Mage::getSingleton('paypal/session')));
    }

    /**
     * @return Iterator<string, array{string, (null|string), bool}>
     */
    public static function provideSaveShippingMethodRequests(): Iterator
    {
        yield 'POST without form key' => ['POST', null, false];
        yield 'POST with wrong form key' => ['POST', 'wrong', false];
        yield 'GET without form key' => ['GET', null, false];
        yield 'POST with valid form key' => ['POST', 'valid', true];
    }

    /**
     * @dataProvider provideSaveShippingMethodRequests
     * @group Controller
     */
    public function testSaveShippingMethodRequiresFormKey(string $method, ?string $formKey, bool $allowed): void
    {
        $request = $this->createRequest('saveShippingMethod', $method, $formKey);
        $request->setParam('shipping_method', 'flatrate_flatrate');

        $this->createSubject($request, $allowed)->saveShippingMethodAction();

        self::assertSame([self::INVALID_FORM_KEY], $this->getErrors(Mage::getSingleton('paypal/session')));
    }

    /**
     * @return Iterator<string, array{(null|string), (null|string), bool}>
     */
    public static function provideReturnRequests(): Iterator
    {
        yield 'no session token, no request token' => [null, null, false];
        yield 'no session token, request token' => [null, 'EC-OTHER', false];
        yield 'session token, no request token' => ['EC-SESSION', null, false];
        yield 'session token, wrong request token' => ['EC-SESSION', 'EC-OTHER', false];
        yield 'session token, matching request token' => ['EC-SESSION', 'EC-SESSION', true];
    }

    /**
     * @dataProvider provideReturnRequests
     * @group Controller
     */
    public function testReturnRequiresTheExpressCheckoutToken(
        ?string $sessionToken,
        ?string $requestToken,
        bool $allowed
    ): void {
        if ($sessionToken !== null) {
            Mage::getSingleton('paypal/session')->setExpressCheckoutToken($sessionToken);
        }

        $request = $this->createRequest('return', 'GET', null);
        if ($requestToken !== null) {
            $request->setQuery('token', $requestToken);
        }

        $this->createSubject($request, $allowed)->returnAction();

        self::assertSame([self::WRONG_TOKEN], $this->getErrors(Mage::getSingleton('checkout/session')));
    }

    private function createRequest(string $action, string $method, ?string $formKey): Mage_Core_Controller_Request_Http
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $request = new Mage_Core_Controller_Request_Http('http://localhost/paypal/express/' . $action);
        $request->setModuleName('paypal');
        $request->setControllerName('express');
        $request->setActionName($action);
        if ($formKey !== null) {
            $value = $formKey === 'valid' ? Mage::getSingleton('core/session')->getFormKey() : 'wrong-form-key';
            $method === 'POST' ? $request->setPost('form_key', $value) : $request->setQuery('form_key', $value);
        }

        return $request;
    }

    /**
     * @return MockObject&Subject
     */
    private function createSubject(Mage_Core_Controller_Request_Http $request, bool $allowed): Subject
    {
        $subject = $this->getMockBuilder(Subject::class)
            ->setConstructorArgs([$request, new Mage_Core_Controller_Response_Http()])
            ->onlyMethods(['_initCheckout'])
            ->getMock();

        if ($allowed) {
            $subject->expects(self::once())->method('_initCheckout')->willThrowException(new Error(self::PASSED_GUARD));
            $this->expectException(Error::class);
            $this->expectExceptionMessage(self::PASSED_GUARD);
        } else {
            $subject->expects(self::never())->method('_initCheckout');
        }

        return $subject;
    }

    /**
     * @return list<string>
     */
    private function getErrors(Mage_Core_Model_Session_Abstract $session): array
    {
        $errors = [];
        foreach ($session->getMessages(true)->getItems() as $message) {
            if ($message->getType() === Mage_Core_Model_Message::ERROR) {
                $errors[] = $message->getText();
            }
        }

        return $errors;
    }
}
