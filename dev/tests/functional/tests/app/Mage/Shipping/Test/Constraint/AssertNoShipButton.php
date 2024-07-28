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

namespace Mage\Shipping\Test\Constraint;

use Mage\Sales\Test\Constraint\AbstractAssertNoButtonOnOrderPage;

/**
 * Assert that 'Ship' button is absent in order page.
 */
class AssertNoShipButton extends AbstractAssertNoButtonOnOrderPage
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Button name for verify.
     *
     * @var string
     */
    protected $buttonName = 'Ship';

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Ship button is absent on order view page.';
    }
}
