<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model;

use Mage;
use Mage_Core_Exception;
use Mage_Customer_Model_Customer as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer\CustomerTrait;

/**
 * @phpstan-import-type ValidateData from CustomerTrait
 * @phpstan-import-type ValidateMethods from CustomerTrait
 */
final class CustomerTest extends OpenMageTest
{
    use CustomerTrait;

    private static Subject $subject;

    protected function setUp(): void
    {
        self::$subject = Mage::getModel('customer/customer');
    }

    /**
     * @dataProvider provideValidateCustomerData
     * @group Model
     * @param string[]|true $expectedResult
     * @psalm-param ValidateData    $data
     * @psalm-param ValidateMethods $methods
     * @throws Mage_Core_Exception
     */
    public function testValidate(array|bool $expectedResult, array $data, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);
        $mock->setData($data);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertSame($expectedResult, $mock->validate());
    }

    /**
     * @group Model
     */
    public function testAuthenticateReturnsFalseForInvalidPassword(): void
    {
        $customer = $this->getMockWithCalledMethods(Subject::class, [
            'loadByEmail' => self::WILL_RETURN_SELF,
            'validatePassword' => false,
        ], true);

        self::assertFalse($customer->authenticate('customer@example.com', 'invalid-password'));
    }

    /**
     * @group Model
     */
    public function testAuthenticateReturnsTrueForValidPassword(): void
    {
        $customer = $this->getMockWithCalledMethods(Subject::class, [
            'loadByEmail' => self::WILL_RETURN_SELF,
            'validatePassword' => true,
        ], true);
        $customer->setPasswordHash(Mage::helper('core')->getHash('valid-password', true));

        self::assertTrue($customer->authenticate('customer@example.com', 'valid-password'));
    }

    /**
     * @group Model
     */
    public function testAuthenticateThrowsForUnconfirmedCustomer(): void
    {
        $customer = $this->getMockWithCalledMethods(Subject::class, [
            'loadByEmail' => self::WILL_RETURN_SELF,
            'isConfirmationRequired' => true,
        ], true);
        $customer->setConfirmation('confirmation-token');

        $this->expectException(Mage_Core_Exception::class);
        $this->expectExceptionCode(Subject::EXCEPTION_EMAIL_NOT_CONFIRMED);

        $customer->authenticate('customer@example.com', 'valid-password');
    }

    /**
     * @dataProvider provideGetDobData
     * @group Model
     */
    public function testGetDob($expectedResult, ?string $dob): void
    {
        self::assertNull(self::$subject->getDob());

        self::$subject->setDob($dob);
        self::assertSame($expectedResult, self::$subject->getDob());
    }
}
