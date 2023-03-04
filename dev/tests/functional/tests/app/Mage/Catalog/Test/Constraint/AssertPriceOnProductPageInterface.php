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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Constraint;

use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Catalog\Test\Block\Product\View;

/**
 * Interface for Constraints price on product page classes.
 */
interface AssertPriceOnProductPageInterface
{
    /**
     * Verify product price on product view page.
     *
     * @param InjectableFixture $product
     * @param View $productViewBlock
     * @return void
     */
    public function assertPrice(InjectableFixture $product, View $productViewBlock);

    /**
     * Set $errorMessage for constraint.
     *
     * @param string $errorMessage
     * @return void
     */
    public function setErrorMessage($errorMessage);
}
