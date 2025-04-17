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
use OpenMage\Tests\Unit\OpenMageTest;

class TemplateTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('newsletter/template');
    }

    /**
     * @dataProvider validateTemplateDataProvider
     * @group Model
     */
    public function testValidate(?string $expected, array $methods): void
    {
        self::$subject->setTemplateCode($methods['setTemplateCode']);
        self::$subject->setTemplateSenderEmail($methods['setTemplateSenderEmail']);
        self::$subject->setTemplateSenderName($methods['setTemplateSenderName']);
        self::$subject->setTemplateSubject($methods['setTemplateSubject']);
        self::$subject->setTemplateText($methods['setTemplateText']);
        self::$subject->setTemplateType($methods['setTemplateType']);

        if ($expected) {
            $this->expectException(Mage_Core_Exception::class);
            $this->expectExceptionMessage($expected);
        } else {
            $this->expectNotToPerformAssertions();
        }

        self::$subject->validate();
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
            'You must give a non-empty value for field \'template_sender_email\'',
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
     * @group Model
     */
    public function testLoadByCode(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->loadByCode('test_code'));
    }

    /**
     * @group Model
     */
    public function testIsValidForSend(): void
    {
        static::assertIsBool(self::$subject->isValidForSend());
    }

    /**
     * @group Model
     */
    public function testGetProcessedTemplate(): void
    {
        static::assertIsString(self::$subject->getProcessedTemplate(['key' => 'value']));
    }
}
