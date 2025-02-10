<?php
/**
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @dataProvider provideGetStoreId
 * @group Mage_Catalog
 * @group Mage_Catalog_Model
 * @group Mage_Catalog_Model_Resource
 */
declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Resource\Eav;

use Mage;
use Mage_Catalog_Model_Resource_Eav_Attribute as Subject;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\CoreTrait;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    use CoreTrait;

    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('catalog/resource_eav_attribute');
    }

    
    public function testGetStoreId($expectedResult, $withStoreId): void
    {
        if ($withStoreId) {
            $this->subject->setStoreId($withStoreId);
        }
        $this->assertSame($expectedResult, $this->subject->getStoreId());
    }
}
