<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Block\Catalog\Form\Renderer\Fieldset;

use Generator;
use Mage_Catalog_Model_Product;
use Mage_Catalog_Model_Resource_Eav_Attribute;

trait ElementTrait
{
    /**
     * @return Generator<string, list{bool, Mage_Catalog_Model_Resource_Eav_Attribute, Mage_Catalog_Model_Product}, void, void>
     */
    public static function provideIsGlobalAttributeOnStoreScopeData(): Generator
    {
        yield 'global attribute, store view scope' => [
            true,
            (new Mage_Catalog_Model_Resource_Eav_Attribute())
                ->setIsGlobal(Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL),
            (new Mage_Catalog_Model_Product())->setStoreId(1),
        ];

        yield 'global attribute, default values scope (store id 0)' => [
            false,
            (new Mage_Catalog_Model_Resource_Eav_Attribute())
                ->setIsGlobal(Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL),
            (new Mage_Catalog_Model_Product())->setStoreId(0),
        ];

        yield 'website-scope attribute, store view scope' => [
            false,
            (new Mage_Catalog_Model_Resource_Eav_Attribute())
                ->setIsGlobal(Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE),
            (new Mage_Catalog_Model_Product())->setStoreId(1),
        ];

        yield 'store-scope attribute, store view scope' => [
            false,
            (new Mage_Catalog_Model_Resource_Eav_Attribute())
                ->setIsGlobal(Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE),
            (new Mage_Catalog_Model_Product())->setStoreId(1),
        ];
    }
}
