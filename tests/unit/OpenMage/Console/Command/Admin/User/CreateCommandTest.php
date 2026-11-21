<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console\Command\Admin\User;

use Mage;
use Mage_Admin_Model_User;
use OpenMage\Console\Application;
use OpenMage\Tests\Unit\OpenMage\Console\Command\Admin\User\Fixtures\FailingRoleUserResource;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class CreateCommandTest extends TestCase
{
    private const USERNAME = 'openmage_console_test_user';

    private const EMAIL = 'openmage_console_test_user@example.com';

    private const USER_RESOURCE_KEY = '_resource_singleton/admin/user';

    private string $password;

    protected function setUp(): void
    {
        Mage::app();
        $this->password = bin2hex(random_bytes(8)) . 'a1';
        $this->deleteTestUser();
    }

    protected function tearDown(): void
    {
        Mage::unregister(self::USER_RESOURCE_KEY);
        $this->deleteTestUser();
    }

    public function testCreatesActiveUserWithGivenRole(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::SUCCESS, $tester->execute($this->arguments(['role' => 'Administrators'])));
        self::assertStringContainsString(self::USERNAME, $tester->getDisplay());

        $user = $this->loadTestUser();
        self::assertNotNull($user->getId());
        self::assertSame(1, (int) $user->getIsActive());
        self::assertSame(self::EMAIL, $user->getEmail());
        self::assertSame('Administrators', $user->getRole()->getRoleName());
        self::assertTrue(Mage::helper('core')->validateHash($this->password, $user->getPassword()));
    }

    public function testDefaultRoleIsAdministrators(): void
    {
        self::assertSame(Command::SUCCESS, $this->createTester()->execute($this->arguments()));

        self::assertSame('Administrators', $this->loadTestUser()->getRole()->getRoleName());
    }

    public function testUnknownRoleFailsWithoutCreatingUser(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::FAILURE, $tester->execute($this->arguments(['role' => 'openmage_console_unknown_role']), ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertStringContainsString('openmage_console_unknown_role', $tester->getErrorOutput());
        self::assertNull($this->loadTestUser()->getId());
    }

    public function testInvalidDataFailsWithModelMessages(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::FAILURE, $tester->execute($this->arguments(['email' => 'not-an-email', 'password' => 'abc']), ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertStringContainsString('Please enter a valid email.', $tester->getErrorOutput());
        // The adminhtml translation rewords this message once it is loaded: assert the part both wordings share.
        self::assertStringContainsString('Password must be at least', $tester->getErrorOutput());
        self::assertNull($this->loadTestUser()->getId());
    }

    public function testExistingUserIsLeftUntouched(): void
    {
        self::assertSame(Command::SUCCESS, $this->createTester()->execute($this->arguments()));
        $passwordHash = $this->loadTestUser()->getPassword();

        $tester = $this->createTester();
        self::assertSame(Command::FAILURE, $tester->execute($this->arguments(['password' => $this->password . 'b2', 'lastname' => 'Changed']), ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertStringContainsString('already exists', $tester->getErrorOutput());

        $user = $this->loadTestUser();
        self::assertSame($passwordHash, $user->getPassword());
        self::assertSame('User', $user->getLastname());
    }

    public function testFailedRoleAssignmentDoesNotKeepTheUser(): void
    {
        Mage::unregister(self::USER_RESOURCE_KEY);
        Mage::register(self::USER_RESOURCE_KEY, new FailingRoleUserResource());

        try {
            $this->createTester()->execute($this->arguments());
            self::fail('The role assignment did not fail.');
        } catch (RuntimeException $runtimeException) {
            self::assertSame(FailingRoleUserResource::MESSAGE, $runtimeException->getMessage());
        }

        self::assertNull($this->loadTestUser()->getId());
    }

    public function testAsksForMissingArgumentsWithConfirmedPassword(): void
    {
        $tester = $this->createTester();
        $tester->setInputs([self::EMAIL, $this->password, $this->password, 'Console', 'User']);

        self::assertSame(Command::SUCCESS, $tester->execute(['username' => self::USERNAME]));
        self::assertStringNotContainsString($this->password, $tester->getDisplay());
        self::assertTrue(Mage::helper('core')->validateHash($this->password, $this->loadTestUser()->getPassword()));
    }

    public function testMismatchedPasswordConfirmationFails(): void
    {
        $tester = $this->createTester();
        $tester->setInputs([$this->password, $this->password . 'x']);

        self::assertSame(Command::FAILURE, $tester->execute($this->arguments(['password' => null]), ['capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertStringContainsString('Password confirmation must be same as password.', $tester->getErrorOutput());
        self::assertNull($this->loadTestUser()->getId());
    }

    public function testMissingArgumentFailsWhenNotInteractive(): void
    {
        $tester = $this->createTester();

        self::assertSame(Command::FAILURE, $tester->execute(['username' => self::USERNAME], ['interactive' => false, 'capture_stderr_separately' => true]));
        self::assertSame('', $tester->getDisplay());
        self::assertStringContainsString('email', $tester->getErrorOutput());
        self::assertNull($this->loadTestUser()->getId());
    }

    /**
     * @param  array<string, null|string> $overrides
     * @return array<string, string>
     */
    private function arguments(array $overrides = []): array
    {
        return array_filter(array_merge([
            'username' => self::USERNAME,
            'email' => self::EMAIL,
            'password' => $this->password,
            'firstname' => 'Console',
            'lastname' => 'User',
        ], $overrides), static fn(?string $value): bool => $value !== null);
    }

    private function createTester(): CommandTester
    {
        return new CommandTester(Application::create()->find('admin:user:create'));
    }

    private function loadTestUser(): Mage_Admin_Model_User
    {
        return Mage::getModel('admin/user')->loadByUsername(self::USERNAME);
    }

    private function deleteTestUser(): void
    {
        $user = $this->loadTestUser();
        if ($user->getId() !== null) {
            $user->delete();
        }
    }
}
