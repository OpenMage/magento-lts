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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
