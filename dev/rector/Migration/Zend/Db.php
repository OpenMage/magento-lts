<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Zend;

use Laminas\Db\Sql\Select;
use Rector\Renaming\ValueObject\RenameClassAndConstFetch;

final class Db
{
    /**
     * @return RenameClassAndConstFetch[]
     */
    public static function renameClassConst(): array
    {
        return [
            new RenameClassAndConstFetch('Zend_Db_Select', 'SQL_ASC', Select::class, 'ORDER_ASCENDING'),
            new RenameClassAndConstFetch('Zend_Db_Select', 'SQL_DESC', Select::class, 'ORDER_DESCENDING'),
        ];
    }
}
