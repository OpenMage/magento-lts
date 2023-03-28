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

namespace Mage\Checkout\Test\Constraint;

use Mage\Checkout\Test\Fixture\CheckoutAgreement;
use Mage\Checkout\Test\Page\Adminhtml\CheckoutAgreementIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Check that checkout agreement is present in agreement grid.
 */
class AssertTermInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that checkout agreement is present in agreement grid.
     *
     * @param CheckoutAgreementIndex $agreementIndex
     * @param CheckoutAgreement $checkoutAgreement
     * @return void
     */
    public function processAssert(CheckoutAgreementIndex $agreementIndex, CheckoutAgreement $checkoutAgreement)
    {
        $agreementIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $agreementIndex->getAgreementGridBlock()->isRowVisible(['name' => $checkoutAgreement->getName()]),
            'Checkout Agreement "' . $checkoutAgreement->getName() . '" is absent in agreement grid.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Checkout Agreement is present in agreement grid.';
    }
}
