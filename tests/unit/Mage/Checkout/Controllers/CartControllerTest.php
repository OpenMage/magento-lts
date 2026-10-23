<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Checkout\Controllers;

use Mage;
use Mage_Checkout_CartController as Subject;
use Mage_Core_Block_Messages;
use Mage_Core_Controller_Request_Http;
use Mage_Core_Controller_Response_Http;
use Mage_Core_Exception;
use OpenMage\Tests\Unit\OpenMageTest;

final class CartControllerTest extends OpenMageTest
{
    /**
     * @group Controller
     * @group runInSeparateProcess
     * @runInSeparateProcess
     * @throws Mage_Core_Exception
     */
    public function testEstimatePostSanitizesInvalidCountryIdErrorMessage(): void
    {
        $unsanitaryText = '<script>alert("xss")</script>';

        $request = new Mage_Core_Controller_Request_Http('http://localhost/checkout/cart/estimatePost');
        $request->setParam('country_id', $unsanitaryText);
        // Need to set request's action name to anything b/c CartController's implementation assumes it's not null
        $request->setActionName('test');

        $response = new Mage_Core_Controller_Response_Http();

        (new Subject($request, $response))->estimatePostAction();

        // When the country_id is invalid, the controller writes an error to the
        // session and redirects the client back to the previous page, which
        // will render a Mage_Core_Block_Messages. If the invalid country_id is
        // rendered in a message without being sanitized, the test fails.

        $session = Mage::getSingleton('checkout/session');

        /** @var Mage_Core_Block_Messages $block */
        $block = Mage::getModel('core/layout')->createBlock('core/messages', 'test_xss_messages');
        $block->addMessages($session->getMessages(true));

        $html = $block->toHtml();

        self::assertStringNotContainsString(
            '<script',
            $html,
            'XSS payload in country_id was rendered unescaped in message output',
        );
    }
}
