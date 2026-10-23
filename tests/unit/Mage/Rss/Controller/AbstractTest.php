<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Rss\Controller;

use Mage_Core_Controller_Response_Http;
use Mage_Rss_Controller_Abstract as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use ReflectionMethod;

final class AbstractTest extends OpenMageTest
{
    /**
     * The response is created with a default text/html header and setHeader() appends
     * by default, so the feed content type has to replace it. A feed served as HTML is
     * parsed by the browser, which turns the contents of a CDATA section into markup.
     *
     * @group Controller
     */
    public function testSetFeedContentTypeReplacesDefaultHtmlHeader(): void
    {
        $response = new Mage_Core_Controller_Response_Http();
        $response->headersSentThrowsException = false;
        $response->setHeader('Content-Type', 'text/html; charset=UTF-8');

        $subject = $this->getMockWithCalledMethods(Subject::class, ['getResponse' => $response]);

        $method = new ReflectionMethod(Subject::class, '_setFeedContentType');
        $method->invoke($subject);

        $headers = [];
        foreach ($response->getHeaders() as $header) {
            $headers[strtolower((string) $header['name'])][] = $header['value'];
        }

        self::assertSame(['text/xml; charset=UTF-8'], $headers['content-type']);
        self::assertSame(['nosniff'], $headers['x-content-type-options']);
    }
}
