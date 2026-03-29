<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Mage;

use Mage_Catalog_CategoryController;
use Mage_Catalog_Helper_Category_Flat;
use Mage_Catalog_Helper_Image;
use Mage_Catalog_Model_Product_Attribute_Backend_Tierprice;
use Mage_Catalog_Model_Product_Flat_Flag;
use Mage_Catalog_Model_Product_Option_Type_Default;
use Mage_Catalog_Model_Resource_Category_Flat;
use Mage_Catalog_Model_Resource_Eav_Attribute;
use Mage_Catalog_Model_Resource_Product_Collection;
use Mage_Catalog_Model_Url;
use Rector\Arguments\ValueObject\ReplaceArgumentDefaultValue;
use Rector\Renaming\ValueObject\MethodCallRename;

final class Catalog
{
    /**
     * @return MethodCallRename[]
     */
    public static function renameMethod(): array
    {
        return [
            new MethodCallRename(Mage_Catalog_CategoryController::class, '_initCatagory', '_initCategory'),
            new MethodCallRename(Mage_Catalog_Helper_Category_Flat::class, 'isRebuilt', 'isBuilt'),
            new MethodCallRename(Mage_Catalog_Helper_Image::class, 'getOriginalHeigh', 'getOriginalHeight'),
            new MethodCallRename(Mage_Catalog_Model_Product_Attribute_Backend_Tierprice::class, '_getWebsiteRates', '_getWebsiteCurrencyRates'),
            new MethodCallRename(Mage_Catalog_Model_Product_Flat_Flag::class, 'setIsBuild', 'setIsBuilt'),
            new MethodCallRename(Mage_Catalog_Model_Product_Option_Type_Default::class, 'getQuoteItem', 'getConfigurationItem'),
            new MethodCallRename(Mage_Catalog_Model_Product_Option_Type_Default::class, 'getQuoteItemOption', 'getConfigurationItemOption'),
            new MethodCallRename(Mage_Catalog_Model_Resource_Category_Flat::class, 'isRebuilt', 'isBuilt'),
            new MethodCallRename(Mage_Catalog_Model_Resource_Eav_Attribute::class, '_getLabelForStore', 'getFrontendLabel'),
            new MethodCallRename(Mage_Catalog_Model_Resource_Product_Collection::class, 'addMinimalPrice', 'addPriceData'),
            new MethodCallRename(Mage_Catalog_Model_Resource_Product_Collection::class, 'addFinalPrice', 'addPriceData'),
            new MethodCallRename(Mage_Catalog_Model_Url::class, 'getUnusedPath', 'getUnusedPathByUrlKey'),
        ];
    }

    /**
     * @return ReplaceArgumentDefaultValue[]
     */
    public static function replaceArgumentDefaultValue(): array
    {
        return [
            new ReplaceArgumentDefaultValue(Mage_Catalog_Model_Resource_Product_Collection::class, 'addAttributeToSort', 1, 'asc', 'ASC'),
            new ReplaceArgumentDefaultValue(Mage_Catalog_Model_Resource_Product_Collection::class, 'addAttributeToSort', 1, 'desc', 'DESC'),
        ];
    }
}
