<?php

/**
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Resource\Eav;

use Generator;
use Mage;
use Mage_Catalog_Model_Resource_Eav_Attribute;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    public Mage_Catalog_Model_Resource_Eav_Attribute $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('catalog/resource_eav_attribute');
    }

    /**
     * @dataProvider provideGetStoreId
     * @group Mage_Catalog
     * @group Mage_Catalog_Model
     * @group Mage_Catalog_Model_Resource
     */
    public function testGetStoreId($expectedResult, $withStoreId): void
    {
        if ($withStoreId) {
            $this->subject->setStoreId($withStoreId);
        }
        $this->assertSame($expectedResult, $this->subject->getStoreId());
    }

    public function provideGetStoreId(): Generator
    {
        yield 'string' => [
            1,
            '1',
        ];
        yield 'int' => [
            1,
            1,
        ];
        yield 'no store id' => [
            null,
            null,
        ];
    }
}
