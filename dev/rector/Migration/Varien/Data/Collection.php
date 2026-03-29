<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Varien\Data;

use Rector\Arguments\ValueObject\ReplaceArgumentDefaultValue;
use Varien_Data_Collection;
use Varien_Data_Collection_Db;

final class Collection
{
    /**
     * @return ReplaceArgumentDefaultValue[]
     */
    public static function replaceArgumentDefaultValue(): array
    {
        return [
            new ReplaceArgumentDefaultValue(Varien_Data_Collection::class, 'setOrder', 1, 'asc', 'ASC'),
            new ReplaceArgumentDefaultValue(Varien_Data_Collection::class, 'setOrder', 1, 'desc', 'DESC'),
            new ReplaceArgumentDefaultValue(Varien_Data_Collection_Db::class, 'setOrder', 1, 'asc', 'ASC'),
            new ReplaceArgumentDefaultValue(Varien_Data_Collection_Db::class, 'setOrder', 1, 'desc', 'DESC'),
        ];
    }
}
