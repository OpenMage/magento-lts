<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ConfigurableSwatches
 */

/**
 * Class Mage_ConfigurableSwatches_Helper_Swatchdimensions
 *
 * @package    Mage_ConfigurableSwatches
 */
class Mage_ConfigurableSwatches_Helper_Swatchdimensions extends Mage_Core_Helper_Abstract
{
    public const AREA_DETAIL = 'product_detail_dimensions';

    public const AREA_LISTING = 'product_listing_dimensions';

    public const AREA_LAYER = 'layered_nav_dimensions';

    public const DIM_WIDTH = 'width';

    public const DIM_HEIGHT = 'height';

    protected $_moduleName = 'Mage_ConfigurableSwatches';

    /**
     * The buffer between the "inner" and "outer" dimensions of a swatch
     *
     * @var int
     */
    protected $_dimensionBuffer = 2;

    /**
     * Get any dimension
     *
     * @param string $area
     * @param string $dimension
     * @param bool $outer
     * @return int
     */
    public function getDimension($area, $dimension, $outer = false)
    {
        $dimension = Mage::getStoreConfigAsInt(
            Mage_ConfigurableSwatches_Helper_Data::CONFIG_PATH_BASE . '/' . $area . '/' . $dimension,
        );
        if ($outer) {
            $dimension += $this->_dimensionBuffer;
        }

        return $dimension;
    }

    /**
     * Get inner width for any area
     *
     * @param string $area
     * @return int
     */
    public function getInnerWidth($area)
    {
        return $this->getDimension($area, self::DIM_WIDTH);
    }

    /**
     * Get inner height for any area
     *
     * @param string $area
     * @return int
     */
    public function getInnerHeight($area)
    {
        return $this->getDimension($area, self::DIM_HEIGHT);
    }

    /**
     * Get outer width for any area
     *
     * @param string $area
     * @return int
     */
    public function getOuterWidth($area)
    {
        return $this->getDimension($area, self::DIM_WIDTH, true);
    }

    /**
     * Get outer height for any area
     *
     * @param string $area
     * @return int
     */
    public function getOuterHeight($area)
    {
        return $this->getDimension($area, self::DIM_HEIGHT, true);
    }
}
