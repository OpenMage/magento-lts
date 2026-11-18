<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Override;
use Mage;
use Mage_Core_Exception;
use Mage_Core_Model_Variable as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;

final class VariableTest extends OpenMageTest
{
    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/variable');
    }

    /**
     * @group Model
     */
    public function testGetVariablesOptionArray(): void
    {
        self::assertIsArray(self::$subject->getVariablesOptionArray());
    }

    public static function provideValidCodes(): iterable
    {
        yield 'letters' => ['store_phone'];
        yield 'mixed case and digits' => ['Block5'];
        yield 'hyphen' => ['id-4'];
        yield 'slash' => ['footer/links'];
    }

    public static function provideInvalidCodes(): iterable
    {
        yield 'script tag' => ['<script>alert(1)</script>'];
        yield 'img tag' => ['x<img src=x onerror=alert(1)>'];
        yield 'quote' => ["a'b"];
        yield 'html entity' => ['a&#39;b'];
        yield 'whitespace' => ['a b'];
        yield 'directive end' => ['a}}b'];
        yield 'leading digit' => ['1abc'];
        yield 'leading underscore' => ['_abc'];
        yield 'trailing newline' => ["abc\n"];
    }

    /**
     * @group Model
     */
    #[DataProvider('provideValidCodes')]
    public function testIsValidCodeAcceptsCodes(string $code): void
    {
        self::assertTrue(Subject::isValidCode($code));
    }

    /**
     * @group Model
     */
    #[DataProvider('provideInvalidCodes')]
    public function testIsValidCodeRejectsCodes(string $code): void
    {
        self::assertFalse(Subject::isValidCode($code));
    }

    /**
     * The form validation (AJAX) rejects the code before the uniqueness lookup.
     *
     * @group Model
     */
    #[DataProvider('provideInvalidCodes')]
    public function testValidateRejectsInvalidCode(string $code): void
    {
        $variable = Mage::getModel('core/variable')->setData(['code' => $code, 'name' => 'Name']);
        $result = $variable->validate();
        self::assertIsString($result);
        self::assertStringContainsString('Variable Code must start with a letter', $result);
    }

    /**
     * Saving rejects a new variable with an invalid code, whatever the form did.
     *
     * @group Model
     */
    #[DataProvider('provideInvalidCodes')]
    public function testBeforeSaveRejectsInvalidCodeOnNewVariable(string $code): void
    {
        $variable = Mage::getModel('core/variable')->setData(['code' => $code, 'name' => 'Name']);
        $this->expectException(Mage_Core_Exception::class);
        $this->callBeforeSave($variable);
    }

    /**
     * Changing the code of an existing variable to an invalid one is rejected too.
     *
     * @group Model
     */
    public function testBeforeSaveRejectsChangeToInvalidCode(): void
    {
        $variable = Mage::getModel('core/variable')->setData(['variable_id' => 7, 'code' => 'old_code', 'name' => 'Name']);
        $variable->setOrigData();
        $variable->setCode('<script>alert(1)</script>');
        $this->expectException(Mage_Core_Exception::class);
        $this->callBeforeSave($variable);
    }

    /**
     * A variable stored with a code the rule does not allow can still be edited if the code is kept.
     *
     * @group Model
     */
    public function testBeforeSaveKeepsUnchangedLegacyCode(): void
    {
        $variable = Mage::getModel('core/variable')->setData(['variable_id' => 7, 'code' => 'legacy.code', 'name' => 'Name']);
        $variable->setOrigData();
        $variable->setName('New name');
        $this->callBeforeSave($variable);
        self::assertSame('legacy.code', $variable->getCode());
    }

    /**
     * @group Model
     */
    #[DataProvider('provideValidCodes')]
    public function testBeforeSaveAcceptsValidCode(string $code): void
    {
        $variable = Mage::getModel('core/variable')->setData(['code' => $code, 'name' => 'Name']);
        $this->callBeforeSave($variable);
        self::assertTrue($variable->isObjectNew());
    }

    private function callBeforeSave(Subject $variable): void
    {
        $method = new ReflectionMethod($variable, '_beforeSave');
        $method->invoke($variable);
    }
}
