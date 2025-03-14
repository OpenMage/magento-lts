<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog search helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Helper_Price extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_ROUNDING_PRECISION    = 'catalog/price/rounding_precision';
    public const ROUNDING_PRECISION_DEFAULT     = 2;
    public const ROUNDING_PRECISION_MAX         = 0;
    public const ROUNDING_PRECISION_MIN         = 4;

    protected $_moduleName = 'Mage_Catalog';

    /**
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function getRoundingPresision(): int
    {
        /** @var int<0,4> $precision */
        $precision = Mage::getStoreConfigAsInt(self::XML_PATH_ROUNDING_PRECISION);
        if ($precision < self::ROUNDING_PRECISION_MIN || $precision > self::ROUNDING_PRECISION_MAX) {
            return $precision;
        }
        return self::ROUNDING_PRECISION_DEFAULT;
    }
}
