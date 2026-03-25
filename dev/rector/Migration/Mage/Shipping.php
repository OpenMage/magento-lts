<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Mage;

use Mage_Shipping_Model_Resource_Carrier_Tablerate;
use Rector\Renaming\ValueObject\MethodCallRename;

final class Shipping
{
    /**
     * @return MethodCallRename[]
     */
    public static function renameMethod(): array
    {
        return [
            new MethodCallRename(Mage_Shipping_Model_Resource_Carrier_Tablerate::class, '_isPositiveDecimalNumber', '_parseDecimalValue'),
        ];
    }
}
