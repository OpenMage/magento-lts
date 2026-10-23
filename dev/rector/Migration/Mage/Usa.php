<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Mage;

use Mage_Usa_Model_Shipping_Carrier_Usps;
use Rector\Renaming\ValueObject\MethodCallRename;

final class Usa
{
    /**
     * @return MethodCallRename[]
     */
    public static function renameMethod(): array
    {
        return [
            new MethodCallRename(Mage_Usa_Model_Shipping_Carrier_Usps::class, 'setTrackingReqeust', 'setTrackingRequest'),
        ];
    }
}
