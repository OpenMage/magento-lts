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
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Newsletter\Model;

use Generator;
use Mage;
use Mage_Core_Exception;
use Mage_Newsletter_Model_Template as Subject;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('newsletter/template');
    }

    /**
     * @dataProvider validateTemplateDataProvider
     * @group Mage_Newsletter
     * @group Mage_Newsletter_Model
     */
    public function testValidate(?string $expected, array $methods): void
    {
        $this->subject->setTemplateCode($methods['setTemplateCode']);
        $this->subject->setTemplateSenderEmail($methods['setTemplateSenderEmail']);
        $this->subject->setTemplateSenderName($methods['setTemplateSenderName']);
        $this->subject->setTemplateSubject($methods['setTemplateSubject']);
        $this->subject->setTemplateText($methods['setTemplateText']);
        $this->subject->setTemplateType($methods['setTemplateType']);

        if ($expected) {
            $this->expectException(Mage_Core_Exception::class);
            $this->expectExceptionMessage($expected);
        } else {
            $this->expectNotToPerformAssertions();
        }

        $this->subject->validate();
    }

    public function validateTemplateDataProvider(): Generator
    {
        $validData = [
            'setTemplateCode' => 'Valid Code',
            'setTemplateSenderEmail' => 'test@example.com',
            'setTemplateSenderName' => 'Sender Name',
            'setTemplateSubject' => 'Valid Subject',
            'setTemplateText' => 'Valid Template Text',
            'setTemplateType' => 1,
        ];

        yield 'valid data' => [
            null,
            $validData,
        ];

        $data = $validData;
        $data['setTemplateCode'] = null;
        yield 'missing template code' => [
            'You must give a non-empty value for field \'template_code\'',
            $data,
        ];

        $data = $validData;
        $data['setTemplateSenderEmail'] = null;
        yield 'missing sender email' => [
            '\'invalid-email\' is not a valid email address in the basic format local-part@hostname',
            $data,
        ];

        $data = $validData;
        $data['setTemplateSenderEmail'] = 'invalid-email';
        yield 'invalid sender email' => [
            '\'invalid-email\' is not a valid email address in the basic format local-part@hostname',
            $data,
        ];

        $data = $validData;
        $data['setTemplateSenderName'] = null;
        yield 'missing sender name' => [
            'You must give a non-empty value for field \'template_sender_name\'',
            $data,
        ];

        $data = $validData;
        $data['setTemplateSubject'] = null;
        yield 'missing template subject' => [
            null,
            $data,
        ];

        $data = $validData;
        $data['setTemplateText'] = null;
        yield 'missing template text' => [
            null,
            $data,
        ];

        $data = $validData;
        $data['setTemplateType'] = null;
        yield 'missing template type' => [
            'You must give a non-empty value for field \'template_type\'',
            $data,
        ];
    }

    /**
     * @group Mage_Newsletter
     * @group Mage_Newsletter_Model
     */
    public function testLoadByCode(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject->loadByCode('test_code'));
    }

    /**
     * @group Mage_Newsletter
     * @group Mage_Newsletter_Model
     */
    public function testIsValidForSend(): void
    {
        $this->assertIsBool($this->subject->isValidForSend());
    }

    /**
     * @group Mage_Newsletter
     * @group Mage_Newsletter_Model
     */
    public function testGetProcessedTemplate(): void
    {
        $this->assertIsString($this->subject->getProcessedTemplate(['key' => 'value']));
    }
}
