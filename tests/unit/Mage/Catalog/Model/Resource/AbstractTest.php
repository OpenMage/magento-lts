<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Resource;

use Mage;
use Mage_Catalog_Model_Resource_Abstract as Subject;
use Mage_Catalog_Model_Resource_Eav_Attribute;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Resource\AbstractTrait;
use ReflectionMethod;
use ReflectionProperty;

final class AbstractTest extends OpenMageTest
{
    use AbstractTrait;

    /**
     * Mage_Eav_Model_Entity_Abstract calls _updateAttribute() for every changed attribute value,
     * so saving a product or category must not raise a deprecation notice (#5841)
     *
     * @group Model
     */
    public function testUpdateAttributeWithoutDeprecationNotice(): void
    {
        $subject = Mage::getResourceModel('catalog/product');
        $attribute = $subject->getAttribute('name');
        self::assertInstanceOf(Mage_Catalog_Model_Resource_Eav_Attribute::class, $attribute);

        $product = Mage::getModel('catalog/product')
            ->setId(1)
            ->setStoreId(0);

        $deprecations = [];
        set_error_handler(static function (int $errno, string $errstr) use (&$deprecations): bool {
            $deprecations[] = $errstr;
            return true;
        }, E_USER_DEPRECATED | E_DEPRECATED);

        try {
            (new ReflectionMethod(Subject::class, '_updateAttribute'))
                ->invoke($subject, $product, $attribute, 1, 'Updated name');
        } finally {
            restore_error_handler();
        }

        self::assertSame([], $deprecations);

        $valuesToSave = (new ReflectionProperty(Subject::class, '_attributeValuesToSave'))->getValue($subject);
        $table = $attribute->getBackend()->getTable();
        self::assertSame('Updated name', $valuesToSave[$table][0]['value']);
        self::assertSame(1, $valuesToSave[$table][0]['entity_id']);
    }
}
