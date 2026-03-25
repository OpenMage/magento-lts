<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Mage;

use Mage_CatalogSearch_Model_Query;
use Rector\Renaming\ValueObject\MethodCallRename;

final class CatalogSearch
{
    /**
     * @return MethodCallRename[]
     */
    public static function renameMethod(): array
    {
        return [
            new MethodCallRename(Mage_CatalogSearch_Model_Query::class, 'getMinQueryLenght', 'getMinQueryLength'),
            new MethodCallRename(Mage_CatalogSearch_Model_Query::class, 'getMaxQueryLenght', 'getMaxQueryLength'),
        ];
    }
}
