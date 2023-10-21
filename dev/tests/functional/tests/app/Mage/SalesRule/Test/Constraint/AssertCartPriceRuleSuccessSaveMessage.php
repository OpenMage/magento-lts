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

namespace Mage\SalesRule\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;

/**
 * Assert that after save a Sales Rule successful message appears.
 */
class AssertCartPriceRuleSuccessSaveMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success Sales Rule save message.
     */
    const SUCCESS_MESSAGE = 'The rule has been saved.';

    /**
     * Assert that success message is displayed after sales rule save.
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @return void
     */
    public function processAssert(PromoQuoteIndex $promoQuoteIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $promoQuoteIndex->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Sales rule success save message is present.';
    }
}
