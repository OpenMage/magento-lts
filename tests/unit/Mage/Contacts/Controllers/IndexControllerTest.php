<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Contacts\Controllers;

use Mage;
use Mage_Contacts_IndexController as Subject;
use Mage_Core_Exception;
use Mage_Customer_Model_Session;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Contacts\Controllers\IndexControllerTrait;

class IndexControllerTest extends OpenMageTest
{
    use IndexControllerTrait;

    /**
     * @dataProvider providePostActionData
     * @group Controller
     * @group runInSeparateProcess
     * @runInSeparateProcess
     * @throws Mage_Core_Exception
     */
    public function testPostAction(array $postData, bool $isFormKeyValid, ?string $expectedErrorMessage): void
    {
        $requestMock = $this->getMockBuilder(\Mage_Core_Controller_Request_Http::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPost'])
            ->getMock();
        $requestMock->method('getPost')->willReturn($postData);

        $subject = $this->getMockBuilder(Subject::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['_validateFormKey', 'getRequest', '_redirect'])
            ->getMock();
        $subject->method('getRequest')->willReturn($requestMock);
        $subject->method('_validateFormKey')->willReturn($isFormKeyValid);

        $sessionMock = $this->getMockBuilder(Mage_Customer_Model_Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addError', 'addSuccess'])
            ->getMock();

        Mage::register('_singleton/customer/session', $sessionMock);

        if ($expectedErrorMessage) {
            $sessionMock->expects(static::once())
                ->method('addError')
                ->with($expectedErrorMessage);
        } else {
            $sessionMock->expects(static::once())
                ->method('addSuccess')
                ->with('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.');
        }

        $subject->expects(static::once())->method('_redirect')->with('*/*/');
        $subject->postAction();

        Mage::unregister('_singleton/customer/session');
    }
}
