<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Controller\Rss;

use Mage_Adminhtml_Controller_Rss_Abstract as Subject;
use Mage_Core_Controller_Response_Http;
use OpenMage\Tests\Unit\OpenMageTest;
use ReflectionMethod;

final class AbstractTest extends OpenMageTest
{
    /**
     * @see \OpenMage\Tests\Unit\Mage\Rss\Controller\AbstractTest
     *
     * @group Controller
     */
    public function testSetFeedContentTypeReplacesDefaultHtmlHeader(): void
    {
        $response = new Mage_Core_Controller_Response_Http();
        $response->headersSentThrowsException = false;
        $response->setHeader('Content-Type', 'text/html; charset=UTF-8');

        $subject = $this->createPartialMock(Subject::class, ['getResponse']);
        $subject->method('getResponse')->willReturn($response);

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
