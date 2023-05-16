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
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Tax\Test\Constraint;

use Mage\Tax\Test\Page\Adminhtml\TaxRateIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that success delete message is displayed after tax rate has been deleted.
 */
class AssertTaxRateSuccessDeleteMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Text for verify.
     */
    const SUCCESS_DELETE_MESSAGE = 'The tax rate has been deleted.';

    /**
     * Assert that success delete message is displayed after tax rate has been deleted.
     *
     * @param TaxRateIndex $taxRateIndex
     * @return void
     */
    public function processAssert(TaxRateIndex $taxRateIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $taxRateIndex->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Returns string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Tax rate success delete message is present.';
    }
}
