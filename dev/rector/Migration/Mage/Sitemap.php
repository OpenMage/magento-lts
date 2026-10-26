<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Mage;

use Mage_Sitemap_Model_Resource_Catalog_Category;
use Mage_Sitemap_Model_Resource_Catalog_Product;
use Rector\Renaming\ValueObject\MethodCallRename;

final class Sitemap
{
    /**
     * @return MethodCallRename[]
     */
    public static function renameMethod(): array
    {
        return [

            new MethodCallRename(Mage_Sitemap_Model_Resource_Catalog_Category::class, '_prepareCategory', '_prepareObject'),
            new MethodCallRename(Mage_Sitemap_Model_Resource_Catalog_Product::class, '_prepareProduct', '_prepareObject'),
        ];
    }
}
