<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Mage;

use Mage_Paypal_Model_Api_Abstract;
use Rector\Renaming\ValueObject\MethodCallRename;

final class Paypal
{
    /**
     * @return MethodCallRename[]
     */
    public static function renameMethod(): array
    {
        return [
            new MethodCallRename(Mage_Paypal_Model_Api_Abstract::class, 'getDebug', 'getDebugFlag'),
        ];
    }
}
