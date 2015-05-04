<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_ConfigurableSwatches_Helper_Swatchdimensions
 */
class Mage_ConfigurableSwatches_Helper_Swatchdimensions extends Mage_Core_Helper_Abstract
{
    const AREA_DETAIL = 'product_detail_dimensions';
    const AREA_LISTING = 'product_listing_dimensions';
    const AREA_LAYER = 'layered_nav_dimensions';

    const DIM_WIDTH = 'width';
    const DIM_HEIGHT = 'height';

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
        $dimension = (int) Mage::getStoreConfig(
            Mage_ConfigurableSwatches_Helper_Data::CONFIG_PATH_BASE . '/' . $area . '/' . $dimension);
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
