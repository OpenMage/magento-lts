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

namespace Mage\CurrencySymbol\Test\Constraint;

use Mage\CurrencySymbol\Test\Page\Adminhtml\SystemCurrencySymbolIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Check that after clicking on 'Save Currency Symbols' button success message appears.
 */
class AssertCurrencySymbolSuccessSaveMessage extends AbstractConstraint
{
    /**
     * Text value to be checked.
     */
    const SUCCESS_SAVE_MESSAGE = 'Custom currency symbols were applied successfully.';

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after clicking on 'Save Currency Symbols' button success message appears.
     *
     * @param SystemCurrencySymbolIndex $currencySymbolIndex
     * @return void
     */
    public function processAssert(SystemCurrencySymbolIndex $currencySymbolIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $currencySymbolIndex->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return 'Currency Symbol success save message is correct.';
    }
}
