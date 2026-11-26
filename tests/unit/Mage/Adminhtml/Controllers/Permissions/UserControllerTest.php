<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Controllers\Permissions;

use Mage;
use Mage_Admin_Model_Session;
use Mage_Admin_Model_User;
use Mage_Adminhtml_Permissions_UserController as Subject;
use Mage_Core_Controller_Request_Http;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Controllers\Permissions\UserControllerTrait;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

final class UserControllerTest extends OpenMageTest
{
    use UserControllerTrait;

    private const SESSION_KEY = '_singleton/admin/session';

    private mixed $previousSession = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = Mage::registry(self::SESSION_KEY);
    }

    protected function tearDown(): void
    {
        Mage::unregister(self::SESSION_KEY);
        if ($this->previousSession !== null) {
            Mage::register(self::SESSION_KEY, $this->previousSession);
        }

        parent::tearDown();
    }

    /**
     * The saved user is the loaded one, and an admin keeps the status and role of their own account.
     *
     * @dataProvider providePrepareUserData
     * @param array<string, mixed> $post
     * @param array<string, mixed> $expectedData
     * @group Controller
     */
    public function testPrepareUserData(?int $loadedUserId, int $sessionUserId, array $post, array $expectedData, bool $expectError): void
    {
        $sessionUser = (new Mage_Admin_Model_User())->setIdFieldName('user_id')->setId($sessionUserId);
        /** @var Mage_Admin_Model_Session $session */
        $session = (new ReflectionClass(Mage_Admin_Model_Session::class))->newInstanceWithoutConstructor();
        $session->setData('user', $sessionUser);
        Mage::unregister(self::SESSION_KEY);
        Mage::register(self::SESSION_KEY, $session);

        $model = $this->createPartialMock(Mage_Admin_Model_User::class, ['getRoles']);
        $model->method('getRoles')->willReturn(['3']);
        $model->setIdFieldName('user_id')->setId($loadedUserId);

        $request = new Mage_Core_Controller_Request_Http();
        $request->setParam('roles', $post['roles'] ?? false);

        $subject = $this->createPartialMock(Subject::class, []);
        (new ReflectionProperty($subject, '_request'))->setValue($subject, $request);

        [$data, $error] = (new ReflectionMethod($subject, '_prepareUserData'))->invoke($subject, $model, $post);

        self::assertSame($expectedData, $data);
        self::assertSame($expectError, $error !== null);
    }
}
