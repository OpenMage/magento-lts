<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Contacts\Controllers;

use Generator;
use Mage;
use Mage_Contacts_IndexController as Subject;
use PHPUnit\Framework\TestCase;

class IndexControllerTest extends TestCase
{
    protected function setUp(): void
    {
        Mage::app();
    }

    /**
     * @dataProvider postActionDataProvider
     * @group Mage_Contacts
     * @group Mage_Contacts_Controller
     * @runInSeparateProcess
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

        $sessionMock = $this->getMockBuilder(\Mage_Customer_Model_Session::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addError', 'addSuccess'])
            ->getMock();

        Mage::register('_singleton/customer/session', $sessionMock);

        if ($expectedErrorMessage) {
            $sessionMock->expects($this->once())
                ->method('addError')
                ->with($this->equalTo($expectedErrorMessage));
        } else {
            $sessionMock->expects($this->once())
                ->method('addSuccess')
                ->with($this->equalTo('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
        }

        $subject->expects($this->once())->method('_redirect')->with('*/*/');
        $subject->postAction();

        Mage::unregister('_singleton/customer/session');
    }

    public function postActionDataProvider(): Generator
    {
        $validData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'comment' => 'Test comment',
        ];

        $error = 'Unable to submit your request. Please, try again later';

        yield 'valid data' => [
            $validData,
            true,
            null,
        ];

        yield 'invalid form key' => [
            $validData,
            false,
            'Invalid Form Key. Please submit your request again.',
        ];

        $data = $validData;
        $data['name'] = '';
        yield 'missing name' => [
            $data,
            true,
            $error,
        ];

        $data = $validData;
        $data['email'] = '';
        yield 'missing email' => [
            $data,
            true,
            $error,
        ];

        $data = $validData;
        $data['email'] = 'invalid-email';
        yield 'invalid email' => [
            $data,
            true,
            $error,
        ];

        $data = $validData;
        $data['comment'] = '';
        yield 'missing comment' => [
            $data,
            true,
            $error,
        ];
    }
}
