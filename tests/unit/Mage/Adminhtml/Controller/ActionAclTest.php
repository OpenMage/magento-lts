<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Controller;

use Mage;
use Mage_Admin_Model_Acl;
use Mage_Admin_Model_Session;
use Mage_Admin_Model_User;
use Mage_Adminhtml_Controller_Action;
use Mage_Adminhtml_Sales_Order_CreateController;
use Mage_Adminhtml_Sales_Order_EditController;
use Mage_Adminhtml_TagController;
use Mage_Adminhtml_Tax_ClassController;
use Mage_Core_Controller_Request_Http;
use Mage_Core_Controller_Response_Http;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Controller\ActionAclTrait;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Varien_Object;

/**
 * Admin controllers must check the ACL leaf the role editor offers for each action,
 * not only the parent that every holder of a sibling leaf also has.
 */
final class ActionAclTest extends OpenMageTest
{
    use ActionAclTrait;

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
     * @dataProvider provideActionAclData
     * @param class-string<Mage_Adminhtml_Controller_Action> $controllerClass
     * @param array<string, string>                          $params
     * @param string[]                                       $granted
     * @group Controller
     */
    public function testActionAcl(string $controllerClass, string $action, array $params, array $granted, bool $expected): void
    {
        $this->loginWithRole($granted);
        $controller = new $controllerClass(new Mage_Core_Controller_Request_Http(), new Mage_Core_Controller_Response_Http());
        $controller->getRequest()->setActionName($action)->setParams($params);

        self::assertSame($expected, $this->isAllowed($controller));
    }

    /**
     * @dataProvider provideOrderCreateAclData
     * @param string[] $granted
     * @group Controller
     */
    public function testOrderCreateAcl(string $action, bool $reordering, array $granted, bool $expected): void
    {
        $this->loginWithRole($granted);
        $controller = $this->createController(Mage_Adminhtml_Sales_Order_CreateController::class, $action, [], ['_getSession']);
        $controller->method('_getSession')->willReturn(new Varien_Object(['reordered' => $reordering ? 12 : null]));

        self::assertSame($expected, $this->isAllowed($controller));
    }

    /**
     * Editing an order is gated on Edit alone, as it was before the parent
     * controller's action map started deciding it.
     *
     * @dataProvider provideOrderEditAclData
     * @param string[] $granted
     * @group Controller
     */
    public function testOrderEditAcl(string $action, array $granted, bool $expected): void
    {
        $this->loginWithRole($granted);
        $controller = $this->createController(Mage_Adminhtml_Sales_Order_EditController::class, $action, [], ['_getSession']);
        $controller->method('_getSession')->willReturn(new Varien_Object());

        self::assertSame($expected, $this->isAllowed($controller));
    }

    /**
     * @dataProvider provideTagAclData
     * @param array<string, mixed> $params
     * @param array<int, int>      $statuses
     * @param string[]             $granted
     * @group Controller
     */
    public function testTagAcl(string $action, array $params, array $statuses, array $granted, bool $expected): void
    {
        $this->loginWithRole($granted);
        $controller = $this->createController(Mage_Adminhtml_TagController::class, $action, $params, ['_getTagStatuses']);
        $controller->method('_getTagStatuses')->willReturnCallback(
            static fn(array $tagIds): array => array_intersect_key($statuses, array_flip($tagIds)),
        );

        self::assertSame($expected, $this->isAllowed($controller));
    }

    /**
     * @dataProvider provideTaxClassAclData
     * @param array<string, string> $post
     * @param string[]              $granted
     * @group Controller
     */
    public function testTaxClassSaveAcl(array $post, ?string $existingType, array $granted, bool $expected): void
    {
        $this->loginWithRole($granted);
        $request = $this->createPartialMock(Mage_Core_Controller_Request_Http::class, ['isPost', 'getPost']);
        $request->method('isPost')->willReturn(true);
        $request->method('getPost')->willReturnCallback(
            static fn(?string $key = null, mixed $default = null): mixed => $key === null ? $post : ($post[$key] ?? $default),
        );
        $request->setActionName('save');

        $controller = $this->createPartialMock(Mage_Adminhtml_Tax_ClassController::class, ['_getClassType']);
        $controller->method('_getClassType')->willReturn($existingType);
        (new ReflectionProperty($controller, '_request'))->setValue($controller, $request);

        self::assertSame($expected, $this->isAllowed($controller));
    }

    /**
     * @template T of Mage_Adminhtml_Controller_Action
     * @param  class-string<T>      $class
     * @param  array<string, mixed> $params
     * @param  string[]             $mockedMethods
     * @return MockObject&T
     */
    private function createController(string $class, string $action, array $params, array $mockedMethods): MockObject
    {
        $controller = $this->createPartialMock($class, $mockedMethods);
        $request = new Mage_Core_Controller_Request_Http();
        $request->setActionName($action)->setParams($params);
        (new ReflectionProperty($controller, '_request'))->setValue($controller, $request);

        return $controller;
    }

    private function isAllowed(Mage_Adminhtml_Controller_Action $controller): bool
    {
        return (new ReflectionMethod($controller, '_isAllowed'))->invoke($controller);
    }

    /**
     * Log in as a user whose role was saved in the role editor with these resources ticked.
     *
     * The editor ticks every ancestor of a ticked resource and writes an explicit
     * allow or deny row for every resource, which is what this reproduces.
     *
     * @param string[] $granted
     */
    private function loginWithRole(array $granted): void
    {
        $acl = new Mage_Admin_Model_Acl();
        Mage::getSingleton('admin/config')->loadAclResources($acl);

        $ticked = [];
        foreach ($granted as $resource) {
            $path = 'admin';
            foreach (explode('/', $resource) as $part) {
                $path .= '/' . $part;
                self::assertTrue($acl->has($path), "Unknown ACL resource {$path}");
                $ticked[$path] = true;
            }
        }

        $rules = [$this->rule('all', 'deny')];
        foreach ($acl->getResources() as $resourceId) {
            if ($resourceId !== 'all') {
                $rules[] = $this->rule($resourceId, isset($ticked[$resourceId]) ? 'allow' : 'deny');
            }
        }

        $resource = Mage::getResourceModel('admin/acl');
        $resource->loadRoles($acl, [
            ['role_id' => 1, 'parent_id' => 0, 'role_type' => 'G', 'user_id' => 0],
            ['role_id' => 2, 'parent_id' => 1, 'role_type' => 'U', 'user_id' => 7],
        ]);
        $resource->loadRules($acl, $rules);

        /** @var Mage_Admin_Model_Session $session */
        $session = (new ReflectionClass(Mage_Admin_Model_Session::class))->newInstanceWithoutConstructor();
        $session->setData('acl', $acl);
        $session->setData('user', new Mage_Admin_Model_User(['user_id' => 7]));

        Mage::unregister(self::SESSION_KEY);
        Mage::register(self::SESSION_KEY, $session);
    }

    /**
     * @return array<string, mixed>
     */
    private function rule(string $resource, string $permission): array
    {
        return [
            'role_id' => 1,
            'role_type' => 'G',
            'resource_id' => $resource,
            'privileges' => '',
            'assert_id' => 0,
            'permission' => $permission,
        ];
    }
}
