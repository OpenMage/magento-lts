<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration\Zend;

use Mage_Core_Helper_Measure_Length;
use Mage_Core_Helper_Measure_Weight;
use Rector\Renaming\ValueObject\RenameClassAndConstFetch;

final class Messure
{
    /**
     * @return RenameClassAndConstFetch[]
     */
    public static function renameClassConst(): array
    {
        return [
            new RenameClassAndConstFetch('Zend_Measure_Length', 'CENTIMETER', Mage_Core_Helper_Measure_Length::class, 'CENTIMETER'),
            new RenameClassAndConstFetch('Zend_Measure_Length', 'INCH', Mage_Core_Helper_Measure_Length::class, 'INCH'),
            new RenameClassAndConstFetch('Zend_Measure_Weight', 'KILOGRAM', Mage_Core_Helper_Measure_Weight::class, 'KILOGRAM'),
            new RenameClassAndConstFetch('Zend_Measure_Weight', 'OUNCE', Mage_Core_Helper_Measure_Weight::class, 'OUNCE'),
            new RenameClassAndConstFetch('Zend_Measure_Weight', 'POUND', Mage_Core_Helper_Measure_Weight::class, 'POUND'),
        ];
    }
}
