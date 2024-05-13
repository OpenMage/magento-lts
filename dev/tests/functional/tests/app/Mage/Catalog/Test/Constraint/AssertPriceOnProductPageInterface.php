<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
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
