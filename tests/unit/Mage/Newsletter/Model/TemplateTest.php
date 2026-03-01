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
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Newsletter\TemplateTrait;

final class TemplateTest extends OpenMageTest
{
    use TemplateTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('newsletter/template');
    }

    /**
     * @dataProvider provideValidateData
     * @group Model
     * @group test
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

    /**
     * @group Model
     */
    public function testLoadByCode(): void
    {
        self::assertInstanceOf(Subject::class, self::$subject->loadByCode('test_code'));
    }

    /**
     * @group Model
     */
    public function testIsValidForSend(): void
    {
        self::assertIsBool(self::$subject->isValidForSend());
    }

    /**
     * @group Model
     */
    public function testGetProcessedTemplate(): void
    {
        self::assertIsString(self::$subject->getProcessedTemplate(['key' => 'value']));
    }
}
