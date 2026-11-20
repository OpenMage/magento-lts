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
use Mage_Adminhtml_Sales_Order_InvoiceController;
use Mage_Adminhtml_System_Convert_GuiController;
use Mage_Adminhtml_TagController;
use Mage_Adminhtml_Tax_ClassController;
use Mage_Core_Controller_Request_Http;
use Mage_Core_Controller_Response_Http;
use Mage_Dataflow_Model_Batch;
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
     * Editing an order is gated on Edit alone, but only while an edit is in progress:
     * without one the inherited quote steps create an order and need Create or Reorder.
     *
     * @dataProvider provideOrderEditAclData
     * @param array<string, mixed>  $sessionData
     * @param array<string, string> $params
     * @param string[]              $granted
     * @group Controller
     */
    public function testOrderEditAcl(string $action, array $sessionData, array $params, array $granted, bool $expected): void
    {
        $this->loginWithRole($granted);
        $controller = $this->createController(Mage_Adminhtml_Sales_Order_EditController::class, $action, $params, ['_getSession']);
        $controller->method('_getSession')->willReturn(new Varien_Object($sessionData));

        self::assertSame($expected, $this->isAllowed($controller));
    }

    /**
     * The create controller shares the session quote with the edit controller, so it gates an edit in progress on Edit.
     *
     * @dataProvider provideOrderCreateDuringEditAclData
     * @param array<string, mixed>  $sessionData
     * @param array<string, string> $params
     * @param string[]              $granted
     * @group Controller
     */
    public function testOrderCreateAclDuringEdit(string $action, array $sessionData, array $params, array $granted, bool $expected): void
    {
        $this->loginWithRole($granted);
        $controller = $this->createController(Mage_Adminhtml_Sales_Order_CreateController::class, $action, $params, ['_getSession']);
        $controller->method('_getSession')->willReturn(new Varien_Object($sessionData));

        self::assertSame($expected, $this->isAllowed($controller));
    }

    /**
     * The Dataflow Profiles (GUI) permission reaches only wizard-built profiles and their batches.
     *
     * @dataProvider provideDataflowGuiAclData
     * @param array<string, string> $params
     * @param array<string, string> $post
     * @param string[]              $granted
     * @group Controller
     */
    public function testDataflowGuiAcl(string $action, array $params, array $post, array $granted, bool $expected): void
    {
        $this->loginWithRole($granted);
        $controller = $this->createController(
            Mage_Adminhtml_System_Convert_GuiController::class,
            $action,
            $params,
            ['_getGuiEntityType', '_loadBatch'],
        );
        // Profile 1 was built with the wizard, profile 2 is an Advanced Profile, profile 3 was deleted
        $controller->method('_getGuiEntityType')->willReturnCallback(static fn(int $profileId): ?string => $profileId === 1 ? 'product' : null);
        // Batch N ran profile N with the wizard's product adapter, batch 4 ran profile 1 with another adapter
        $controller->method('_loadBatch')->willReturnCallback(static fn(int $batchId): Mage_Dataflow_Model_Batch => new Mage_Dataflow_Model_Batch([
            'batch_id' => $batchId,
            'profile_id' => $batchId === 4 ? 1 : $batchId,
            'adapter' => $batchId === 4 ? 'catalog/product' : 'catalog/convert_adapter_product',
        ]));

        $previousPost = $_POST;
        $controller->getRequest()->setPost($post);
        try {
            self::assertSame($expected, $this->isAllowed($controller));
        } finally {
            $_POST = $previousPost;
        }
    }

    /**
     * Global Search shows a module's results only to the permission that opens its grid,
     * as Mage_Adminhtml_IndexController::globalSearchAction() checks it.
     *
     * @dataProvider provideGlobalSearchAclData
     * @param string[] $granted
     * @group Controller
     */
    public function testGlobalSearchAcl(string $module, array $granted, bool $expected): void
    {
        $this->loginWithRole(['global_search', ...$granted]);
        $config = Mage::getConfig();
        self::assertNotNull($config);
        $acl = (string) $config->getNode("adminhtml/global_search/$module/acl");

        self::assertNotSame('', $acl);
        self::assertSame($expected, Mage::getSingleton('admin/session')->isAllowed($acl));
    }

    /**
     * The invoice form offers online capture and Create Shipment only to the matching permission.
     *
     * @dataProvider provideInvoiceSaveOptionsData
     * @param string[] $granted
     * @param string[] $expectedKeys
     * @group Controller
     */
    public function testInvoiceSaveOptions(array $granted, array $expectedKeys): void
    {
        $this->loginWithRole($granted);
        $controller = $this->createController(Mage_Adminhtml_Sales_Order_InvoiceController::class, 'save', [], []);
        $data = ['capture_case' => 'online', 'do_shipment' => '1', 'comment_text' => 'note'];

        $filtered = (new ReflectionMethod($controller, '_filterInvoiceData'))->invoke($controller, $data);

        self::assertSame($expectedKeys, array_keys($filtered));
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
                self::assertTrue($acl->has($path), "Unknown ACL resource $path");
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
