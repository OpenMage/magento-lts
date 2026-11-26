<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Controllers\System\Convert;

use Mage;
use Mage_Admin_Model_Session;
use Mage_Adminhtml_Model_Session;
use Mage_Adminhtml_System_Convert_GuiController as Subject;
use Mage_Core_Controller_Request_Http;
use Mage_Dataflow_Model_Profile;
use OpenMage\Tests\Unit\OpenMageTest;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionProperty;

final class GuiControllerTest extends OpenMageTest
{
    private const ADMIN_SESSION_KEY = '_singleton/admin/session';

    private const ADMINHTML_SESSION_KEY = '_singleton/adminhtml/session';

    private const PROFILE_KEY = 'current_convert_profile';

    /** @var array<string, mixed> */
    private array $previous = [];

    /** @var array<mixed> */
    private array $previousPost = [];

    protected function setUp(): void
    {
        parent::setUp();
        foreach ([self::ADMIN_SESSION_KEY, self::ADMINHTML_SESSION_KEY, self::PROFILE_KEY] as $key) {
            $this->previous[$key] = Mage::registry($key);
            Mage::unregister($key);
        }

        $this->previousPost = $_POST;
        $adminhtmlSession = $this->createPartialMock(Mage_Adminhtml_Model_Session::class, ['addError']);
        Mage::register(self::ADMINHTML_SESSION_KEY, $adminhtmlSession);
    }

    protected function tearDown(): void
    {
        $_POST = $this->previousPost;
        foreach ($this->previous as $key => $value) {
            Mage::unregister($key);
            if ($value !== null) {
                Mage::register($key, $value);
            }
        }

        parent::tearDown();
    }

    /**
     * The saved profile is the one _isAllowed() checked, not a profile_id from the post
     *
     * @group Controller
     */
    public function testSaveKeepsTheCheckedProfile(): void
    {
        $subject = $this->createSubject(['_initProfile', '_redirect']);
        $request = $subject->getRequest();
        $request->setParam('profile_id', '1');
        $request->setPost([
            'profile_id' => '2',
            'name' => 'Export',
            'entity_type' => 'product',
            'gui_data' => ['file' => ['type' => 'file']],
            'actions_xml' => '<action type="catalog/product" method="load"/>',
        ]);

        $subject->expects(self::once())->method('_initProfile')->with('profile_id')->willReturnCallback(
            static function () use ($request): bool {
                self::assertSame(1, $request->getParam('profile_id'));
                return false;
            },
        );

        $subject->saveAction();

        self::assertSame(['name', 'entity_type', 'gui_data'], array_keys($request->getPost()));
    }

    /**
     * @group Controller
     */
    public function testSaveRejectsPostWithoutWizardData(): void
    {
        $subject = $this->createSubject(['_initProfile', '_redirect']);
        $subject->getRequest()->setPost(['name' => 'Export', 'entity_type' => '', 'actions_xml' => '<action/>']);

        $subject->expects(self::never())->method('_initProfile');
        $subject->expects(self::once())->method('_redirect')->with('*/*');

        $subject->saveAction();
    }

    /**
     * Without the Advanced Profiles permission a wizard profile runs the XML the wizard generates
     *
     * @group Controller
     */
    public function testRunUsesGeneratedXmlWithoutAdvancedProfiles(): void
    {
        $profile = $this->runProfile(false, $this->exportGuiData());

        self::assertStringContainsString('<action type="catalog/convert_adapter_product" method="load">', $profile->getActionsXml());
        self::assertStringNotContainsString('tampered', $profile->getActionsXml());
    }

    /**
     * @group Controller
     */
    public function testRunKeepsStoredXmlWithAdvancedProfiles(): void
    {
        $profile = $this->runProfile(true, $this->exportGuiData());

        self::assertSame('<action type="tampered"/>', $profile->getActionsXml());
    }

    /**
     * @group Controller
     */
    public function testRunRefusesProfileWithoutWizardData(): void
    {
        $profile = $this->runProfile(false, '', false);

        self::assertSame('<action type="tampered"/>', $profile->getActionsXml());
    }

    /**
     * @param array<string, mixed>|string $guiData
     */
    private function runProfile(bool $mayUseAdvancedProfiles, array|string $guiData, bool $expectRun = true): Mage_Dataflow_Model_Profile
    {
        $session = $this->createPartialMock(Mage_Admin_Model_Session::class, ['isAllowed']);
        $session->method('isAllowed')->willReturnCallback(
            static fn(string $resource): bool => $mayUseAdvancedProfiles || $resource !== 'admin/system/convert/profiles',
        );
        Mage::register(self::ADMIN_SESSION_KEY, $session);

        $profile = new Mage_Dataflow_Model_Profile([
            'profile_id' => 1,
            'entity_type' => 'product',
            'direction' => 'export',
            'data_transfer' => 'file',
            'store_id' => 0,
            'gui_data' => $guiData,
            'actions_xml' => '<action type="tampered"/>',
        ]);

        $subject = $this->createSubject(['_initProfile', '_redirect', 'loadLayout', 'renderLayout']);
        $subject->method('_initProfile')->willReturnCallback(static function () use ($profile, $subject): Subject {
            Mage::register(self::PROFILE_KEY, $profile);
            return $subject;
        });
        $subject->expects($expectRun ? self::once() : self::never())->method('renderLayout');
        $subject->expects($expectRun ? self::never() : self::once())->method('_redirect')->with('*/*/edit', ['id' => 1]);

        $subject->runAction();

        return $profile;
    }

    /**
     * Wizard fields of the Export All Products profile that the Dataflow data install creates
     *
     * @return array<string, mixed>
     */
    private function exportGuiData(): array
    {
        $noFilter = ['from' => '', 'to' => ''];
        return [
            'file' => ['type' => 'file', 'filename' => 'export_all_products.csv', 'path' => 'var/export'],
            'parse' => ['type' => 'csv', 'delimiter' => ',', 'enclose' => '"', 'fieldnames' => 'true'],
            'map' => ['only_specified' => '', 'product' => ['db' => [], 'file' => []]],
            'product' => ['filter' => ['name' => '', 'sku' => '', 'type' => '0', 'price' => $noFilter, 'qty' => $noFilter]],
        ];
    }

    /**
     * @param  string[]           $mockedMethods
     * @return MockObject&Subject
     */
    private function createSubject(array $mockedMethods): MockObject
    {
        $subject = $this->createPartialMock(Subject::class, $mockedMethods);
        (new ReflectionProperty($subject, '_request'))->setValue($subject, new Mage_Core_Controller_Request_Http());

        return $subject;
    }
}
