<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Mage;

use Mage_Core_Block_Abstract;
use Mage_Core_Helper_Abstract;
use Rector\Renaming\ValueObject\MethodCallRename;

final class Core
{
    /**
     * @return MethodCallRename[]
     */
    public static function renameMethod(): array
    {
        return [
            new MethodCallRename(Mage_Core_Block_Abstract::class, 'htmlEscape', 'escapeHtml'),
            new MethodCallRename(Mage_Core_Block_Abstract::class, 'urlEscape', 'escapeUrl'),
            new MethodCallRename(Mage_Core_Helper_Abstract::class, 'htmlEscape', 'escapeHtml'),
            new MethodCallRename(Mage_Core_Helper_Abstract::class, 'urlEscape', 'escapeUrl'),
        ];
    }
}
