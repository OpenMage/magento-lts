<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Mage_ConfigurableSwatches_Helper_Swatchdimensions
 *
 * @category   Mage
 * @package    Mage_ConfigurableSwatches
 * @author     Magento Core Team <core@magentocommerce.com>
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
        $dimension = (int) Mage::getStoreConfig(
            Mage_ConfigurableSwatches_Helper_Data::CONFIG_PATH_BASE . '/' . $area . '/' . $dimension
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
