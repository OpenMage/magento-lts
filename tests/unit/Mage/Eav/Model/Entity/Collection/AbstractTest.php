<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Eav\Model\Entity\Collection;

use Mage;
use Mage_Core_Model_Abstract;
use Mage_Eav_Exception;
use Mage_Eav_Helper_Data;
use Mage_Eav_Model_Config;
use Mage_Eav_Model_Entity_Abstract;
use Mage_Eav_Model_Entity_Attribute;
use Mage_Eav_Model_Entity_Collection_Abstract as Subject;
use Mage_Eav_Model_Resource_Helper_Mysql4;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Eav\Model\Entity\Collection\AbstractTrait;
use ReflectionMethod;
use ReflectionProperty;
use Varien_Db_Adapter_Interface;

final class AbstractTest extends OpenMageTest
{
    use AbstractTrait;

    /** @var array<string, mixed> */
    private array $registry = [];

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        foreach (['_singleton/eav/config', '_resource_helper/eav', '_helper/eav'] as $key) {
            $this->registry[$key] = Mage::registry($key);
            Mage::unregister($key);
        }

        $config = $this->createMock(Mage_Eav_Model_Config::class);
        $config->method('getAttribute')->willReturnCallback(static fn($type, $code) => new Mage_Eav_Model_Entity_Attribute([
            'attribute_code' => is_int($code) ? 'fallback_' . $code : $code,
            'backend_type' => 'varchar',
            'backend_table' => 'test_entity_varchar',
        ]));
        Mage::register('_singleton/eav/config', $config);

        $resourceHelper = $this->createMock(Mage_Eav_Model_Resource_Helper_Mysql4::class);
        $resourceHelper->method('getLoadAttributesSelectGroups')->willReturn([['SELECT attribute_values']]);
        Mage::register('_resource_helper/eav', $resourceHelper);

        $helper = $this->createMock(Mage_Eav_Helper_Data::class);
        $helper->method('__')->willReturnCallback(static fn($message) => $message);
        Mage::register('_helper/eav', $helper);
    }

    #[Override]
    protected function tearDown(): void
    {
        foreach ($this->registry as $key => $value) {
            Mage::unregister($key);
            if ($value !== null) {
                Mage::register($key, $value);
            }
        }
        parent::tearDown();
    }

    #[DataProvider('provideAttributeValues')]
    public function testHydratesEveryMatchingItemWithoutChangingValueTypes(mixed $value): void
    {
        $collection = $this->createCollection(['name' => 10], [[[
            'entity_id' => 1, 'attribute_id' => '10', 'value' => $value,
        ]]]);

        $collection->_loadAttributes();

        foreach ($this->items($collection) as $item) {
            self::assertTrue($item->hasData('name'));
            self::assertSame($value, $item->getData('name'));
        }
    }

    public function testRetainsFirstSelectedCodeForDuplicateAttributeIds(): void
    {
        $collection = $this->createCollection(['first' => 10, 'second' => '10'], [[[
            'entity_id' => 1, 'attribute_id' => 10, 'value' => 'value',
        ]]]);

        $collection->_loadAttributes();

        foreach ($this->items($collection) as $item) {
            self::assertSame('value', $item->getData('first'));
            self::assertFalse($item->hasData('second'));
        }
    }

    public function testAcceptsStaticAttributesWithoutAnAttributeId(): void
    {
        $collection = $this->createCollection(['entity_id' => null, 'name' => '10'], [[[
            'entity_id' => 1, 'attribute_id' => 10, 'value' => 'Example',
        ]]]);

        $collection->_loadAttributes();

        self::assertSame('Example', $this->items($collection)[0]->getData('name'));
    }

    public function testResolvesUnselectedAttributeThroughEavConfiguration(): void
    {
        $collection = $this->createCollection(['name' => 10], [[[
            'entity_id' => 1, 'attribute_id' => 999, 'value' => 'Fallback',
        ]]]);

        $collection->_loadAttributes();

        self::assertSame('Fallback', $this->items($collection)[0]->getData('fallback_999'));
    }

    public function testResolvesEntityIdFieldOncePerAttributeLoad(): void
    {
        $rows = [];
        for ($i = 0; $i < 100; ++$i) {
            $rows[] = ['entity_id' => 1, 'attribute_id' => 10, 'value' => $i];
        }
        $collection = $this->createCollection(['name' => 10], [$rows]);

        $collection->_loadAttributes();

        self::assertSame(99, $this->items($collection)[0]->getData('name'));
    }

    public function testRebuildsMetadataAfterClearingAndChangingSelection(): void
    {
        $collection = $this->createCollection(['before' => 10], [
            [['entity_id' => 1, 'attribute_id' => 10, 'value' => 'First load']],
            [['row_id' => 1, 'attribute_id' => 10, 'value' => 'Second load']],
        ], ['entity_id', 'row_id']);
        $collection->_loadAttributes();
        self::assertSame('First load', $this->items($collection)[0]->getData('before'));

        $collection->clear();
        $this->seedItems($collection);
        $this->setProperty($collection, '_selectAttributes', ['after' => 10]);
        $collection->_loadAttributes();

        foreach ($this->items($collection) as $item) {
            self::assertSame('Second load', $item->getData('after'));
            self::assertFalse($item->hasData('before'));
        }
    }

    public function testRejectsAttributeValuesWithoutAMatchingEntity(): void
    {
        $collection = $this->createCollection(['name' => 10], [[[
            'entity_id' => 999, 'attribute_id' => 10, 'value' => 'Orphan',
        ]]]);

        $this->expectException(Mage_Eav_Exception::class);
        $this->expectExceptionMessage('Data integrity: No header row found for attribute');
        $collection->_loadAttributes();
    }

    public function testSetterRetainsSelectedCodeBeforeAnAttributeLoad(): void
    {
        $collection = $this->createCollection(['selected_name' => 10], [[]]);
        $setter = new ReflectionMethod(Subject::class, '_setItemAttributeValue');

        $setter->invoke($collection, ['entity_id' => 1, 'attribute_id' => '10', 'value' => 'Selected']);

        self::assertSame('Selected', $this->items($collection)[0]->getData('selected_name'));
    }

    public function testSetterRetainsFallbackBeforeAnAttributeLoad(): void
    {
        $collection = $this->createCollection([], [[]]);
        $setter = new ReflectionMethod(Subject::class, '_setItemAttributeValue');

        $setter->invoke($collection, ['entity_id' => 1, 'attribute_id' => 999, 'value' => 'Fallback']);

        self::assertSame('Fallback', $this->items($collection)[0]->getData('fallback_999'));
    }

    /**
     * @param  array<string, null|int|string>    $attributes
     * @param  list<list<array<string, mixed>>>  $loads
     * @param  non-empty-list<string>            $idFields
     * @return Subject<Mage_Core_Model_Abstract>
     */
    private function createCollection(array $attributes, array $loads, array $idFields = ['entity_id']): Subject
    {
        $entity = $this->createMock(Mage_Eav_Model_Entity_Abstract::class);
        $entity->method('getType')->willReturn('test_entity');
        $entity->expects(self::exactly(count($idFields)))->method('getEntityIdField')->willReturn(...$idFields);

        $connection = $this->createMock(Varien_Db_Adapter_Interface::class);
        $connection->method('fetchAll')->willReturn(...$loads);

        $collection = $this->getMockBuilder(Subject::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['_getLoadAttributesSelect', '_addLoadAttributesSelectValues'])
            ->getMock();
        $collection->method('_getLoadAttributesSelect')->willReturn('SELECT attribute_values');
        $collection->method('_addLoadAttributesSelectValues')->willReturn('SELECT attribute_values');
        $collection->setEntity($entity);
        $this->setProperty($collection, '_conn', $connection);
        $this->setProperty($collection, '_selectAttributes', $attributes);
        $this->seedItems($collection);

        return $collection;
    }

    /** @param Subject<Mage_Core_Model_Abstract> $collection */
    private function seedItems(Subject $collection): void
    {
        $items = [];
        for ($i = 0; $i < 2; ++$i) {
            $items[] = $this->getMockBuilder(Mage_Core_Model_Abstract::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['_afterLoad'])
                ->getMock()
                ->setData('entity_id', 1);
        }
        $this->setProperty($collection, '_items', $items);
        $this->setProperty($collection, '_itemsById', [1 => $items]);
    }

    /**
     * @param  Subject<Mage_Core_Model_Abstract> $collection
     * @return list<Mage_Core_Model_Abstract>
     */
    private function items(Subject $collection): array
    {
        return (new ReflectionProperty(Subject::class, '_itemsById'))->getValue($collection)[1];
    }

    /** @param Subject<Mage_Core_Model_Abstract> $collection */
    private function setProperty(Subject $collection, string $property, mixed $value): void
    {
        (new ReflectionProperty(Subject::class, $property))->setValue($collection, $value);
    }
}
