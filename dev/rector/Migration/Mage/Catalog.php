<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Mage;

use Mage_Catalog_Model_Resource_Product_Collection;
use Rector\Renaming\ValueObject\MethodCallRename;

final class Catalog
{
    /**
     * @return MethodCallRename[]
     */
    public static function renameMethod(): array
    {
        return [
            new MethodCallRename(Mage_Catalog_Model_Resource_Product_Collection::class, 'addMinimalPrice', 'addPriceData'),
            new MethodCallRename(Mage_Catalog_Model_Resource_Product_Collection::class, 'addFinalPrice', 'addPriceData'),
        ];
    }
}
