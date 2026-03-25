<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Mage;

use Mage_Eav_Model_Config;
use Rector\Renaming\ValueObject\MethodCallRename;

final class Eav
{
    /**
     * @return MethodCallRename[]
     */
    public static function renameMethod(): array
    {
        return [
            new MethodCallRename(Mage_Eav_Model_Config::class, 'getCollectionAttribute', 'getAttribute'),
        ];
    }
}
