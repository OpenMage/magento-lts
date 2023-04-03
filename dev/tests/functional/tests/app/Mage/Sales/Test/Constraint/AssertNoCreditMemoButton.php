<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sales\Test\Constraint;

/**
 * Assert no 'Credit memo' button the order grid.
 */
class AssertNoCreditMemoButton extends AbstractAssertNoButtonOnOrderPage
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Button name for verify.
     *
     * @var string
     */
    protected $buttonName = 'Credit memo';

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Credit memo button is absent on order view page.';
    }
}
