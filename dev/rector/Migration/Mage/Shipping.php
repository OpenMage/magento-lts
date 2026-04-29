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
use Mage_Usa_Model_Shipping_Carrier_Dhl;
use Mage_Usa_Model_Shipping_Carrier_Fedex;
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
            new MethodCallRename(Mage_Usa_Model_Shipping_Carrier_Dhl::class, 'setTrackingReqeust', 'setTrackingRequest'),
            new MethodCallRename(Mage_Usa_Model_Shipping_Carrier_Fedex::class, 'setTrackingReqeust', 'setTrackingRequest'),
        ];
    }
}
