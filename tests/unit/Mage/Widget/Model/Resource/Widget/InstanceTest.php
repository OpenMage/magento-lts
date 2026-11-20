<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Widget\Model\Resource\Widget;

use Mage;
use Mage_Core_Model_Logger;
use Mage_Core_Model_Resource_Setup;
use Mage_Widget_Model_Resource_Widget_Instance as Subject;
use Mage_Widget_Model_Widget_Instance;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Widget\Model\Resource\Widget\InstanceTrait;
use Override;
use Throwable;
use Varien_Db_Adapter_Pdo_Mysql;
use Zend_Db_Expr;

/**
 * The layout update cleanup of the Mage_Widget 1.6.0.2 data upgrade, against the database
 *
 * Each test runs in a transaction that is rolled back. Without a database connection the tests are skipped.
 */
final class InstanceTest extends OpenMageTest
{
    use InstanceTrait;

    private const DECLARED_TYPE = 'cms/widget_page_link';

    private const UNDECLARED_TYPE = 'core/text_list';

    private const LOGGER_KEY = '_singleton/core/logger';

    private const UPGRADE_FILE = 'app/code/core/Mage/Widget/data/widget_setup/data-upgrade-1.6.0.1-1.6.0.2.php';

    private static Subject $subject;

    private static Varien_Db_Adapter_Pdo_Mysql $adapter;

    private int $foreignKeyChecks = 1;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        try {
            $adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
            if (!$adapter instanceof Varien_Db_Adapter_Pdo_Mysql) {
                self::markTestSkipped('Needs a MySQL database connection.');
            }

            $adapter->fetchOne('SELECT 1');
        } catch (Throwable $throwable) {
            self::markTestSkipped('Needs a database connection: ' . $throwable->getMessage());
        }

        self::$adapter = $adapter;
        self::$subject = Mage::getResourceModel('widget/widget_instance');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->foreignKeyChecks = (int) self::$adapter->fetchOne('SELECT @@FOREIGN_KEY_CHECKS');
        self::$adapter->beginTransaction();
    }

    protected function tearDown(): void
    {
        self::$adapter->rollBack();
        self::$adapter->query('SET FOREIGN_KEY_CHECKS = ' . $this->foreignKeyChecks);
        Mage::unregister(self::LOGGER_KEY);
        parent::tearDown();
    }

    /**
     * Unexpected layout updates are reported before they are deleted, with their links and widget page rows;
     * expected ones, the widget instances and their page groups are kept, and a second run removes nothing.
     *
     * @dataProvider provideCleanupBatchData
     * @group Model
     */
    public function testDeleteUnexpectedLayoutUpdates(int $batchSize, bool $foreignKeyChecks): void
    {
        $declared   = $this->createInstance(self::DECLARED_TYPE);
        $tampered   = $this->createInstance(self::DECLARED_TYPE);
        $undeclared = $this->createInstance(self::UNDECLARED_TYPE);

        $unexpectedXml = Mage_Widget_Model_Widget_Instance::LAYOUT_UPDATE_UNEXPECTED_XML;
        $undeclaredType = Mage_Widget_Model_Widget_Instance::LAYOUT_UPDATE_UNDECLARED_TYPE;
        $templateXml = '<reference name="content"><block type="cms/widget_page_link" name="x">'
            . '<action method="setData"><name>template</name><value>page/html/header.phtml</value></action>'
            . '</block></reference>';

        // Interleaved, so batch boundaries fall between kept and removed updates
        $kept = [];
        $removed = [];
        $kept[] = $this->addLayoutUpdate($declared, $this->generatedXml(self::DECLARED_TYPE));
        $id = $this->addLayoutUpdate($tampered, '<reference name="content"><block type="core/text" name="x"/></reference>');
        $removed[$id] = [$tampered, self::DECLARED_TYPE, $unexpectedXml];
        $kept[] = $this->addLayoutUpdate($declared, '');
        $id = $this->addLayoutUpdate($undeclared, $this->generatedXml(self::UNDECLARED_TYPE));
        $removed[$id] = [$undeclared, self::UNDECLARED_TYPE, $undeclaredType];
        $kept[] = $this->addLayoutUpdate($undeclared, '');
        $kept[] = $this->addLayoutUpdate($tampered, $this->generatedXml(self::DECLARED_TYPE));
        $id = $this->addLayoutUpdate($tampered, $templateXml);
        $removed[$id] = [$tampered, self::DECLARED_TYPE, $unexpectedXml];

        if (!$foreignKeyChecks) {
            self::$adapter->query('SET FOREIGN_KEY_CHECKS = 0');
        }

        $reported = [];
        $deleted = self::$subject->deleteUnexpectedLayoutUpdates(function (array $removals) use (&$reported): void {
            foreach ($removals as $removal) {
                // Reported while the row still exists
                self::assertSame(1, $this->countRows('core/layout_update', $removal['layout_update_id']));
                $reported[$removal['layout_update_id']] = $removal;
            }
        }, $batchSize);

        self::assertSame(count($reported), $deleted);
        self::assertEqualsCanonicalizing(array_keys($removed), array_keys(array_intersect_key($reported, $removed + array_flip($kept))));

        foreach ($removed as $layoutUpdateId => [$instanceId, $type, $reason]) {
            $removal = $reported[$layoutUpdateId];
            self::assertSame($instanceId, $removal['instance_id']);
            self::assertSame($type, $removal['type']);
            self::assertSame($reason, $removal['reason']);
            self::assertSame('default', $removal['handle']);
            self::assertNotSame('', $removal['xml']);
            self::assertSame([['store_id' => 0, 'area' => 'frontend', 'package' => 'base', 'theme' => 'default']], $removal['links']);

            foreach (['core/layout_update', 'core/layout_link', 'widget/widget_instance_page_layout'] as $table) {
                self::assertSame(0, $this->countRows($table, $layoutUpdateId), $table);
            }
        }

        foreach ($kept as $layoutUpdateId) {
            foreach (['core/layout_update', 'core/layout_link', 'widget/widget_instance_page_layout'] as $table) {
                self::assertSame(1, $this->countRows($table, $layoutUpdateId), $table);
            }
        }

        foreach ([$declared, $tampered, $undeclared] as $instanceId) {
            self::assertSame(1, $this->countInstanceRows('widget/widget_instance', $instanceId));
        }

        self::assertSame(3, $this->countInstanceRows('widget/widget_instance_page', $tampered));

        self::assertSame(0, self::$subject->deleteUnexpectedLayoutUpdates(static function (): void {
            self::fail('A second run has nothing to remove.');
        }, $batchSize));
    }

    /**
     * Only linked layout updates without a widget instance whose XML the generator would not produce are reported; nothing is deleted.
     *
     * @group Model
     */
    public function testReportUnownedLayoutUpdates(): void
    {
        $foreign = '<reference name="head"><action method="addJs"><script>test.js</script></action></reference>';
        $instance = $this->createInstance(self::DECLARED_TYPE);

        $unowned = $this->addLayoutUpdate(null, $foreign);
        $notReported = [
            $this->addLayoutUpdate(null, $this->generatedXml(self::DECLARED_TYPE)),
            $this->addLayoutUpdate(null, ''),
            $this->addLayoutUpdate(null, $foreign, false),
            $this->addLayoutUpdate($instance, $foreign),
        ];
        $unownedUndeclared = $this->addLayoutUpdate(null, $this->generatedXml(self::UNDECLARED_TYPE));

        $reported = [];
        $count = self::$subject->reportUnownedLayoutUpdates(static function (array $layoutUpdates) use (&$reported): void {
            foreach ($layoutUpdates as $layoutUpdate) {
                $reported[$layoutUpdate['layout_update_id']] = $layoutUpdate;
            }
        }, 1);

        self::assertSame(count($reported), $count);
        self::assertArrayHasKey($unowned, $reported);
        self::assertArrayHasKey($unownedUndeclared, $reported);
        self::assertSame($foreign, $reported[$unowned]['xml']);
        self::assertSame([['store_id' => 0, 'area' => 'frontend', 'package' => 'base', 'theme' => 'default']], $reported[$unowned]['links']);
        foreach ($notReported as $layoutUpdateId) {
            self::assertArrayNotHasKey($layoutUpdateId, $reported);
        }

        foreach ([$unowned, $unownedUndeclared, ...$notReported] as $layoutUpdateId) {
            self::assertSame(1, $this->countRows('core/layout_update', $layoutUpdateId));
        }
    }

    /**
     * The data upgrade logs each removal with its XML before deleting it, and logs unowned layout updates it keeps.
     *
     * @group Model
     */
    public function testDataUpgrade(): void
    {
        $messages = [];
        $logger = $this->createMock(Mage_Core_Model_Logger::class);
        $logger->method('log')->willReturnCallback(static function (string $message, $level, string $file) use (&$messages): void {
            if ($file === 'widget_instance_cleanup.log') {
                $messages[] = $message;
            }
        });
        Mage::unregister(self::LOGGER_KEY);
        Mage::register(self::LOGGER_KEY, $logger);

        $tamperedXml = '<reference name="content"><block type="core/text" name="x"/></reference>';
        $removed = $this->addLayoutUpdate($this->createInstance(self::DECLARED_TYPE), $tamperedXml);
        $unowned = $this->addLayoutUpdate(null, '<remove name="content"/>');

        $setup = new Mage_Core_Model_Resource_Setup('widget_setup');
        $file = Mage::getBaseDir() . DS . self::UPGRADE_FILE;
        // Bound to the setup object, as Mage_Core_Model_Resource_Setup runs data upgrade files
        $result = (fn() => include $file)->call($setup);

        self::assertNotFalse($result);
        self::assertSame(0, $this->countRows('core/layout_update', $removed));
        self::assertSame(1, $this->countRows('core/layout_update', $unowned));

        $removalMessage = $this->findMessage($messages, sprintf('Removing layout update #%d ', $removed));
        self::assertStringContainsString('unexpected layout update XML. Review the widget instance and save it again', $removalMessage);
        self::assertStringContainsString(json_encode($tamperedXml, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), $removalMessage);
        self::assertStringContainsString('belongs to no widget instance', $this->findMessage($messages, sprintf('Kept layout update #%d:', $unowned)));
    }

    private function createInstance(string $type): int
    {
        $table = self::$subject->getMainTable();
        self::$adapter->insert($table, [
            'instance_type'     => $type,
            'package_theme'     => 'base/default',
            'title'             => 'Layout update cleanup test',
            'store_ids'         => '0',
            'widget_parameters' => serialize([]),
            'sort_order'        => 0,
        ]);

        return (int) self::$adapter->lastInsertId($table);
    }

    /**
     * Add a layout update the way a widget instance page group stores it
     *
     * @param null|int $instanceId widget instance it belongs to, or null for none
     * @param bool     $linked     whether it is linked to a store and theme, so it renders
     */
    private function addLayoutUpdate(?int $instanceId, string $xml, bool $linked = true): int
    {
        $layoutUpdateTable = self::$subject->getTable('core/layout_update');
        self::$adapter->insert($layoutUpdateTable, ['handle' => 'default', 'xml' => $xml, 'sort_order' => 0]);
        $layoutUpdateId = (int) self::$adapter->lastInsertId($layoutUpdateTable);

        if ($linked) {
            self::$adapter->insert(self::$subject->getTable('core/layout_link'), [
                'store_id'         => 0,
                'area'             => 'frontend',
                'package'          => 'base',
                'theme'            => 'default',
                'layout_update_id' => $layoutUpdateId,
            ]);
        }

        if ($instanceId !== null) {
            $pageTable = self::$subject->getTable('widget/widget_instance_page');
            self::$adapter->insert($pageTable, [
                'instance_id'     => $instanceId,
                'page_group'      => 'all_pages',
                'layout_handle'   => 'default',
                'block_reference' => 'content',
                'page_for'        => 'all',
                'entities'        => '',
                'page_template'   => '',
            ]);
            self::$adapter->insert(self::$subject->getTable('widget/widget_instance_page_layout'), [
                'page_id'          => (int) self::$adapter->lastInsertId($pageTable),
                'layout_update_id' => $layoutUpdateId,
            ]);
        }

        return $layoutUpdateId;
    }

    private function generatedXml(string $type): string
    {
        return '<reference name="content"><block type="' . $type . '" name="0123456789abcdef0123456789abcdef">'
            . '<action method="setData"><name>anchor_text</name><value>Fish &amp; chips</value></action>'
            . '</block></reference>';
    }

    private function countRows(string $table, int $layoutUpdateId): int
    {
        return (int) self::$adapter->fetchOne(
            self::$adapter->select()
                ->from(self::$subject->getTable($table), [new Zend_Db_Expr('COUNT(*)')])
                ->where('layout_update_id = ?', $layoutUpdateId),
        );
    }

    private function countInstanceRows(string $table, int $instanceId): int
    {
        return (int) self::$adapter->fetchOne(
            self::$adapter->select()
                ->from(self::$subject->getTable($table), [new Zend_Db_Expr('COUNT(*)')])
                ->where('instance_id = ?', $instanceId),
        );
    }

    /**
     * @param list<string> $messages
     */
    private function findMessage(array $messages, string $prefix): string
    {
        foreach ($messages as $message) {
            if (str_starts_with($message, $prefix)) {
                return $message;
            }
        }

        self::fail('No log message starts with ' . $prefix);
    }
}
