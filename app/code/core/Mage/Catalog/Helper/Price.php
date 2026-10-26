<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog price helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Helper_Price extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_ROUNDING_PRECISION    = 'catalog/price/rounding_precision';

    public const ROUNDING_PRECISION_DEFAULT     = 2;

    public const ROUNDING_PRECISION_MAX         = 4;

    public const ROUNDING_PRECISION_MIN         = 0;

    protected $_moduleName = 'Mage_Catalog';

    /**
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function getRoundingPrecision(): int
    {
        /** @var int<0,4> $precision */
        $precision = Mage::getStoreConfigAsInt(self::XML_PATH_ROUNDING_PRECISION);
        if ($precision < self::ROUNDING_PRECISION_MIN || $precision > self::ROUNDING_PRECISION_MAX) {
            return self::ROUNDING_PRECISION_DEFAULT;
        }

        return $precision;
    }
}
