<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Zend;

use Monolog\Level;
use Rector\Renaming\ValueObject\RenameClassAndConstFetch;

final class Log
{
    /**
     * @return RenameClassAndConstFetch[]
     */
    public static function renameClassConst(): array
    {
        return [
            new RenameClassAndConstFetch('Zend_Log', 'EMERG', Level::class, 'Emergency'),
            new RenameClassAndConstFetch('Zend_Log', 'ALERT', Level::class, 'Alert'),
            new RenameClassAndConstFetch('Zend_Log', 'CRIT', Level::class, 'Critical'),
            new RenameClassAndConstFetch('Zend_Log', 'ERR', Level::class, 'Error'),
            new RenameClassAndConstFetch('Zend_Log', 'WARN', Level::class, 'Warning'),
            new RenameClassAndConstFetch('Zend_Log', 'NOTICE', Level::class, 'Notice'),
            new RenameClassAndConstFetch('Zend_Log', 'INFO', Level::class, 'Info'),
            new RenameClassAndConstFetch('Zend_Log', 'DEBUG', Level::class, 'Debug'),
        ];
    }
}
