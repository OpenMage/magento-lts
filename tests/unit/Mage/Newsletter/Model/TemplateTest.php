<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Newsletter\Model;

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
